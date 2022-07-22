<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @Route({
 *     "en": "/post",
 *     "fr": "/publi",
 * }, methods="GET")
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
     * @Cache(public=true, expires="tomorrow midnight", lastModified="post.getCreatedAt()")
     */
    public function show(
        Request $request,
        Post $post,
        ValidatorInterface $validator,
        AdapterInterface $cache
    ): Response {
        $response = new Response();
        $response
            ->setPublic()
            ->setExpires(new \DateTimeImmutable('tomorrow midnight'))
            ->setLastModified($post->getCreatedAt())
            ->setEtag(md5($post->getBody()))
            ->headers->addCacheControlDirective('no-store')
        ;
        if ($response->isNotModified($request)) {
            // Si non modifié, le statut de la réponse est passé à 304 NOT MODIFIED
            return $response;
        }
        // Non utilisé, juste pour l'exemple
        $subResponse = $this->forward(self::class.'::indexSameCategory', [
            'post' => $post,
        ]);

        $violations = $validator->validate($post);
        $violations = $validator->validate('test@dawan.fr', new Email());
        $violations = $validator->validateProperty($post, 'title');

        $cachedData = $cache->get('app.my-cache-key', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            $data = 'test';

            return $data;
        });

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ], $response);
    }

    public function indexSameCategory(Post $post, PostRepository $repository): Response
    {
        $posts = $repository->sameCategory($post);

        return $this->render('post/index_same_category.html.twig', [
            'posts' => $posts,
        ]);
    }
}
