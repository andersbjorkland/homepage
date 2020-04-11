<?php

namespace App\Controller;

use App\Entity\Drum;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use \RuntimeException;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/drums.json", name="api_drums")
     */
    public function fetchDrums()
    {
        $drumRepository = $this->getDoctrine()->getRepository(Drum::class);
        $drums = $drumRepository->findAll();

        return $this->json($drums);
    }
}
