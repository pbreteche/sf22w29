<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    const POST_LIMIT = 2;

    /**
     * @Route("/post")
     */
    public function index(Request $request, PostRepository $postRepository): Response
    {
        $postsCount = $postRepository->count([]);
        $maxPage = ceil($postsCount / self::POST_LIMIT);
        $pageNumber = $request->query->get('p', 1);
        if (0 > $pageNumber || $maxPage < $pageNumber) {
            $pageNumber = 1;
        }
        $offset = ($pageNumber - 1) * self::POST_LIMIT;
        $posts = $postRepository->findBy([], ['createdAt' => 'DESC'], self::POST_LIMIT, $offset);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'max' => $maxPage,
            'page' => $pageNumber,
        ]);
    }

    /**
     * @Route("/post/{id}", requirements={"id": "\d+"})
     */
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
}
