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

        // accès à la query-string ($_GET)
        $langParam = $request->query->get('lang', 'fr');

        // accès aux données de formulaire ($_POST)
        $langParam = $request->request->get('lang', 'fr');
        $request->files->get('my-file');
        $request->getSession()->get('session-key');
        $request->headers->get('Accept-language');
        $request->getPreferredLanguage(['en-US', 'fr']);

        $response = new Response('ceci est du texte plat', Response::HTTP_OK, [
            'Content-type' => 'text/plain',
        ]);

        $response->headers->set('Content-type', 'text/html');

        return $response;
    }

    /**
     * @Route("/hello/pratical", name="app_hello_practical")
     */
    public function practical1(Request $request): Response
    {
        $currentParam = $request->query->get('param');
        $previousParam = $request->getSession()->get('param');
        $request->getSession()->set('param', $currentParam);

        return new Response('<ul><li>Valeur courante:'.$currentParam.'</li><li>'.$previousParam.'</li></ul></body>');
    }
}
