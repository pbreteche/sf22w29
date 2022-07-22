<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        $request->setLocale('de');
        dump($request->getLocale());
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
    public function show(
        Post $post,
        ValidatorInterface $validator
    ): Response {
        // Non utilisé, juste pour l'exemple
        $subResponse = $this->forward(self::class.'::indexSameCategory', [
            'post' => $post,
        ]);

        $violations = $validator->validate($post);
        $violations = $validator->validate('test@dawan.fr', new Email());
        $violations = $validator->validateProperty($post, 'title');

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
