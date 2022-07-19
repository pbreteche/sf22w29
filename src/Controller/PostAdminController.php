<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            ->add('title')
            ->add('body')
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
}
