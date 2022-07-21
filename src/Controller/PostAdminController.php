<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\DemoService;
use App\Validator\WellFormedTitle;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\EqualTo;

/**
 * @Route("/admin/post")
 * @IsGranted("ROLE_AUTHOR")
 */
class PostAdminController extends AbstractController
{
    /**
     * @Route("/", methods="GET")
     */
    public function index(
        Request $request,
        PostRepository $postRepository,
        DemoService $demo
    ): Response {
        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            $roles = $user->getRoles();
        }

        $demo->sayHello();
        $filterForm = $this->createFormBuilder(null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ])
            ->add('search', TextType::class, [
                'required' => false,
                'constraints' => [
                    new WellFormedTitle(), // non utilisé ici, car on ne valide pas ce formulaire
                ]
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
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }
        $post = new Post();
        $post->setWrittenBy($user);
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
     * @Security("is_granted('ROLE_ADMIN') or is_granted('POST_EDIT', post)")
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

    /**
     * @Route("/delete/{id}", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('POST_DELETE', post)")
     */
    public function delete(Post $post, Request $request, PostRepository $postRepository)
    {
        $form = $this->createFormBuilder()
            ->add('title', TextType::class, [
                'help' => 'Recopier le titre suivant : '.$post->getTitle(),
                'constraints' => new EqualTo($post->getTitle())
            ])
        ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postRepository->remove($post, true);
            $this->addFlash('success', 'La publication a été supprimée.');

            return $this->redirectToRoute('app_postadmin_index');
        }

        return $this->renderForm('post_admin/delete.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }
}
