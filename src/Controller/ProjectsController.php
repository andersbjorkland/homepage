<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProjectsController extends AbstractController {

    /**
     * @Route("/projects", name="app_projects")
     */
    public function index() {
        return $this->render('index.html.twig');
    }

}
