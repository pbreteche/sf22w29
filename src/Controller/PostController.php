<?php

namespace App\Controller;

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
    public function show(int $id, PostRepository $repository)
    {
        $post = $repository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Post id not found in database.');
        }

        /* Exemple de redirection
        return $this->redirect($this->generateUrl('app_post_index'),  Response::HTTP_SEE_OTHER);
        return $this->redirectToRoute('app_post_index', [],  Response::HTTP_SEE_OTHER);
        */

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
}
