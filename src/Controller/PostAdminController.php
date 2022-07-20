<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/post")
 */
class PostAdminController extends AbstractController
{
    /**
     * @Route("/", methods="GET")
     */
    public function index(Request $request, PostRepository $postRepository): Response
    {
        $filterForm = $this->createFormBuilder(null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ])
            ->add('search', TextType::class, [
                'required' => false,
            ])
            ->getForm()
        ;
        $filterForm->handleRequest($request);

        $posts = $postRepository->searchByTitleDQL($filterForm->get('search')->getData() ?? '');

        return $this->render('post_admin/index.html.twig', [
            'posts' => $posts,
            'filter_form' => $filterForm->createView(),
        ]);
    }

    /**
     * @Route("/new", methods={"GET", "POST"})
     */
    public function add(Request $request, PostRepository $repository): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->add($post, true);
            $this->addFlash('success', 'Une nouvelle publication a été enregistrée.');

            return $this->redirectToRoute('app_postadmin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post_admin/add.html.twig', [
            'create_form' => $form,
        ]);
    }

    /**
     * @Route("/edit/{id}", methods={"GET", "POST"})
     */
    public function update(Post $post, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush($post);
            $this->addFlash('success', 'Une publication a été mise-à-jour.');

            return $this->redirectToRoute('app_postadmin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post_admin/edit.html.twig', [
            'edit_form' => $form,
        ]);
    }
}
