<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post_admin/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", methods={"GET", "POST"})
     */
    public function add(Request $request, PostRepository $repository): Response
    {
        $post = new Post();
        $form = $this
            ->createFormBuilder($post)
            ->add('title', null, [
                'label' => 'Titre',
                'help' => 'Définir un titre précis et non-ambigüe.'
            ])
            ->add('body', TextareaType::class, [
                'attr' => [
                    'cols' => 60,
                    'rows' => 15,
                ],
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreatedAt(new \DateTimeImmutable());
            $repository->add($post, true);

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
        $form = $this
            ->createFormBuilder($post)
            ->add('title', null, [
                'label' => 'Titre',
                'help' => 'Définir un titre précis et non-ambigüe.'
            ])
            ->add('body', TextareaType::class, [
                'attr' => [
                    'cols' => 60,
                    'rows' => 15,
                ],
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush($post);

            return $this->redirectToRoute('app_postadmin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post_admin/edit.html.twig', [
            'edit_form' => $form,
        ]);
    }
}
