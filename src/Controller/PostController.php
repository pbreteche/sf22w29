<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post", methods="GET")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(Request $request, PostRepository $postRepository): Response
    {
        $pageNumber = $request->query->get('p', 1);
        $posts = $postRepository->findLatest($pageNumber, $maxPage);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'max' => $maxPage,
            'page' => $pageNumber,
        ]);
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"})
     */
    public function show(Post $post): Response
    {
        // Non utilisé, juste pour l'exemple
        $subResponse = $this->forward(self::class.'::indexSameCategory', [
            'post' => $post,
        ]);

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    public function indexSameCategory(Post $post, PostRepository $repository)
    {
        $posts = $repository->sameCategory($post);

        return $this->render('post/index_same_category.html.twig', [
            'posts' => $posts,
        ]);
    }
}
