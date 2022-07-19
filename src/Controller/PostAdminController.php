<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
