<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloWorldController extends AbstractController
{
    /**
     * @Route("/hello/world", name="app_hello_world")
     */
    public function index(): Response
    {
        return $this->render('hello_world/index.html.twig', [
            'test_twig_variable' => 'Salut tout le monde',
        ]);
    }

    /**
     * @Route("/hello/request", name="app_hello_request")
     */
    public function index2(Request $request): Response
    {
        dump($request->getUri());

        $response = new Response('ceci est du texte plat', Response::HTTP_OK, [
            'Content-type' => 'text/plain',
        ]);

        $response->headers->set('Content-type', 'text/html');

        return $response;
    }
}
