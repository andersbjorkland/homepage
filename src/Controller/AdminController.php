<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\PortfolioEntry;
use App\Form\ImageType;
use App\Form\PortfolioEntryType;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

use \RuntimeException;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function index()
    {
        return $this->render('admin.html.twig');
    }

    /**
     * @Route("/anders/admin")
     */
    public function subIndex() {
        return $this->index();
    }
}
