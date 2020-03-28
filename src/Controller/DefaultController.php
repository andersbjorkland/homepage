<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController {

    /**
     * @Route("/", name="app_home")
     */
    public function index() {
        return $this->render('index.html.twig', [
            'title' => "HELLO",
        ]);
    }

    /**
     * @Route("/anders")
     */
    public function subIndex() {
        return $this->index();
    }

}
