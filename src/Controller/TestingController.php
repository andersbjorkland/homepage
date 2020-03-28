<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class TestingController extends AbstractController {

    /**
     * @Route("/test", name="app_test")
     */
    public function index(Request $request, SluggerInterface $slugger) {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image)
            ->add('save', SubmitType::class, ['label' => 'Upload Image']);
        $form->handleRequest($request);

        $errors = array();

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();


            // this condition is needed because the 'image' field is not required
            // so the image file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $errors[] = $e;
                }

                // updates the 'imageFilename' property to store the image file name
                // instead of its contents
                $image->setFileName($newFilename);
            }
            if (empty($errors)) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($image);
                $entityManager->flush();
            }
            // ... persist the $image variable or any other work

            // return $this->redirect($this->generateUrl('app_home'));
            return $this->render('test.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        return $this->render('test.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}