<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function index()
    {
        $postRepository = $this->getDoctrine()->getRepository(BlogPost::class);
        $unpublished = $postRepository->getUnpublished();
        $published = $postRepository->getLatestPaginated(1, 10);


        return $this->render('admin.html.twig', [
            'unpublished' => $unpublished,
            'published' => $published,
        ]);
    }

    /**
     * @Route("/anders/admin")
     */
    public function subIndex() {
        return $this->index();
    }
}
