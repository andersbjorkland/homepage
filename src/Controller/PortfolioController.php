<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\PortfolioEntry;
use App\Form\PortfolioEntryType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

use \RuntimeException;

class PortfolioController extends AbstractController
{
    /**
     * @Route("/portfolio", name="portfolio")
     */
    public function index()
    {
        $portfolioRepository = $this->getDoctrine()->getRepository(PortfolioEntry::class);
        $projects = $portfolioRepository->findAll();
        return $this->render('portfolio/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("/anders/portfolio")
     */
    public function subIndex() {
        return $this->index();
    }

    /**
     * @Route("/admin/portfolio", name="portfolio_add")
     */
    public function add(Request $request, SluggerInterface $slugger)
    {
        $image = new Image();
        $portfolioEntry = new PortfolioEntry();
        $form = $this->createForm(PortfolioEntryType::class, $portfolioEntry)
            ->add('save', SubmitType::class, ['label' => 'Add Project']);
        $form->handleRequest($request);

        $errors = array();
        $added = false;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            $downloadableFile = $form->get('file')->getData();

            $entityManager = $this->getDoctrine()->getManager();

            

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
                $alt = $form->get('imageAlt')->getData();
                $image->setFileName($newFilename);
                $image->setAlt($alt);
                $entityManager->persist($image);
            }

            if ($downloadableFile) {
                $originalFilename = pathinfo($downloadableFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$downloadableFile->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $downloadableFile->move(
                        $this->getParameter('files_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $errors[] = $e;
                }

                $portfolioEntry->setFilename($newFilename);
            }
            
            try {
                $name = $form->get('name')->getData();
                $portfolioEntry->setName($name);

                $description = $form->get('textDescription')->getData();
                $portfolioEntry->setTextDescription($description);

                $portfolioEntry->setImage($image);
                $entityManager->persist($portfolioEntry);
            } catch (RuntimeException $e) {
                $errors[] = $e;
            }
            

            if (empty($errors)) {
                $entityManager->flush();
                $added = true;
            } 

            return $this->render('portfolio/add.html.twig', [
                'form' => $form->createView(),
                'errors' => $errors,
                'added' => $added
            ]);
        }

        return $this->render('portfolio/add.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
            'added' => $added
        ]);
    }

    /**
     * @Route("admin/portfolio/{id}/edit", name="portfolio_edit")
     */
    public function edit(PortfolioEntry $portfolioEntry, Request $request, SluggerInterface $slugger)
    {
        $image = $portfolioEntry->getImage();
        $edited = false;
        $form = $this->createForm(PortfolioEntryType::class , $portfolioEntry)
            ->add('save', SubmitType::class, ['label' => 'Update Project']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            $downloadableFile = $form->get('file')->getData();

            $entityManager = $this->getDoctrine()->getManager();


            // this condition is needed because the 'image' field is not required
            // so the image file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

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
                $alt = $form->get('imageAlt')->getData();
                $image->setFileName($newFilename);
                $image->setAlt($alt);
                $entityManager->persist($image);
            }

            if ($downloadableFile) {
                $originalFilename = pathinfo($downloadableFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $downloadableFile->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $downloadableFile->move(
                        $this->getParameter('files_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $errors[] = $e;
                }

                $portfolioEntry->setFilename($newFilename);
            }

            try {
                $name = $form->get('name')->getData();
                $portfolioEntry->setName($name);

                $description = $form->get('textDescription')->getData();
                $portfolioEntry->setTextDescription($description);

                $portfolioEntry->setImage($image);
                $entityManager->persist($portfolioEntry);
            } catch (RuntimeException $e) {
                $errors[] = $e;
            }


            if (empty($errors)) {
                $entityManager->flush();
                $edited = true;
            }
        }
        return $this->render('portfolio/edit.html.twig', [
            'form' => $form->createView(),
            'portfolioEntry' => $portfolioEntry,
            'edited' => $edited,
            'errors' => [],
        ]);
    }

    /**
     * @Route("admin/portfolio/{id}/delete", name="portfolio_delete")
     */
    public function delete(PortfolioEntry $portfolioEntry, Request $request)
    {
        $removed = false;

        if ("POST" === $request->getMethod()) {
            $entityManager = $this->getDoctrine()->getManager();


            $this->deleteImageFile($portfolioEntry->getImage());
            $entityManager->remove($portfolioEntry->getImage());
            $portfolioEntry->setImage(null);
            $entityManager->remove($portfolioEntry);

            $entityManager->flush();
            $removed = true;
        }

        return $this->render('portfolio/delete.html.twig', [
            'portfolioEntry' => $portfolioEntry,
            'removed' => $removed
        ]);
    }

    private function deleteImageFile(Image $image) {
        $remove = false;
        $originalFile = new File($this->getParameter('images_directory') .'/'. $image->getFileName());
        if (file_exists($originalFile)) {
            unlink($originalFile);
            $remove = true;
        }

        return $remove;
    }
}
