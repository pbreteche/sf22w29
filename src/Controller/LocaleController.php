<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('locale/index.html.twig', [
            'locales' => ['en', 'fr'],
        ]);
    }

    /**
     * @Route("/locale/{locale}")
     */
    public function select(string $locale, Request $request): Response
    {
        $request->getSession()->set('user.locale', $locale);

        $redirectPath = $request->query->get('path');
        if ($redirectPath) {
            return $this->redirect($redirectPath);
        }

        return $this->redirectToRoute('app_post_index');
    }
}