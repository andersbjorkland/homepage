<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Post;
use App\Entity\User;
use App\Form\BlogType;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

use \DateTime;
use \RuntimeException;

class BlogController extends AbstractController
{


    /**
     * @Route("/anders/admin/blog")
     */
    public function subIndex() {
        return $this->add();
    }

    /**
     * @Route("/admin/blog", name="app_blogging")
     */
    public function add(Request $request, SluggerInterface $slugger)
    {
        $post = new Post();
        $form = $this->createForm(BlogType::class, $post)
            ->add('save', SubmitType::class, ['label' => 'Add Post']);
        $form->handleRequest($request);

        $errors = array();
        $added = false;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $data = $form->getData();
            $imageFile = $form->get('image')->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $now = date("Y-m-d\TH:i:s");
            $post->setEntered(new DateTime("now"));

            // this condition is needed because the 'image' field is not required
            // so the image file must be processed only when a file is uploaded
            if ($imageFile) {
                $image = new Image();
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

                $image->setFileName($newFilename);
                $entityManager->persist($image);
                $post->addImage($image);
                
            }

            $publish = "";
            try {
                $name = $form->get('title')->getData();
                $post->setTitle($name);

                $text = $form->get('text')->getData();
                $post->setText($text);
                $post->setUser($this->getUser());

                if (null !== $form->get('publishTime')) {
                    $publish = $form->get('publishTime')->getData();
                    $post->setPublishTime($publish);
                    $now = $publish;
                } else {
                    $publish = $post->getEntered();
                    $post->setPublishTime($publish);
                }

                $entityManager->persist($post);
            } catch (RuntimeException $e) {
                $errors[] = $e;
            }
            
            if (empty($errors)) {
                $slug = substr($publish->format("Y-m-d H:i:s"), 0, 10);
                $slug .= "_";
                $title_slug = str_replace(" ", "_", $post->getTitle());
                $slug .= $title_slug;
                $post->setSlug($slug);
            }

            if (empty($errors)) {
                $entityManager->flush();
                $added = true;
            } 

            return $this->render('blog/add.html.twig', [
                'form' => $form->createView(),
                'errors' => $errors,
                'added' => $added
            ]);
        }

        return $this->render('blog/add.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
            'added' => $added
        ]);
    }

    /**
     * @Route("/admin/blog/edit/{id}", name="post_edit")
     */
    public function edit(Request $request, SluggerInterface $slugger, Post $post)
    {
        $form = $this->createForm(BlogType::class, $post)
            ->add('save', SubmitType::class, ['label' => 'Update Post']);
        $form->handleRequest($request);

        $errors = array();
        $added = false;

        $messages = array();
        $imageName = "No current image";
        if (null !== $post->getImages()) {
            $images = $post->getImages();
            foreach ($images as $i) {
                $imageName = $i->getFileName();
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $data = $form->getData();
            $imageFile = $form->get('image')->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $now = date("Y-m-d\TH:i:s");
            $post->setEntered(new DateTime("now"));

            if (count($post->getImages()) > 0 && $imageFile) {
                if ($imageFile->getClientOriginalName() !== $post->getImages()[0]) {
                    $originalFile = new File($this->getParameter('images_directory') . $post->getImages()[0]->getFileName());
                    if (file_exists($originalFile)) {
                        unlink($originalFile);

                        $post->removeImage($post->getImages()[0]);
                        $messages[] = "Removed a previous image.";
                    }
                }
            }

            // this condition is needed because the 'image' field is not required
            // so the image file must be processed only when a file is uploaded
            if ($imageFile) {
                $image = new Image();
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

                $image->setFileName($newFilename);
                $entityManager->persist($image);
                $post->addImage($image);
                
            }

            $publish = "";
            try {
                $name = $form->get('title')->getData();
                $post->setTitle($name);

                $text = $form->get('text')->getData();
                $post->setText($text);
                $post->setUser($this->getUser());

                if (null !== $form->get('publishTime')) {
                    $publish = $form->get('publishTime')->getData();
                    $post->setPublishTime($publish);
                    $now = $publish;
                    $messages[] = "Updated Publishing Time";
                } else {
                    $publish = $post->getEntered();
                    $post->setPublishTime($publish);
                    $messages[] = $data->getPublishTime()->format("Y-m-d H:i:s");
                }

                $entityManager->persist($post);
            } catch (RuntimeException $e) {
                $errors[] = $e;
            }
            
            if (empty($errors)) {
                $slug = substr($publish->format("Y-m-d H:i:s"), 0, 10);
                $slug .= "_";
                $title_slug = str_replace(" ", "_", $post->getTitle());
                $slug .= $title_slug;
                $post->setSlug($slug);
            }

            if (empty($errors)) {
                $entityManager->flush();
                $added = true;
            } 
        }

        return $this->render('blog/edit.html.twig', [
            'image' => $imageName,
            'form' => $form->createView(),
            'messages' => $messages,
            'errors' => $errors,
            'added' => $added
        ]);
    }

    /**
     * @Route("/admin/blog/delete/{id}", name="post_delete")
     */
    public function delete(Request $request, Post $post) {
        $removed = false;

        if ("POST" === $request->getMethod()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
            $removed = true;
        }

        return $this->render('blog/delete.html.twig', [
            'post' => $post,
            'removed' => $removed
        ]);
        
    }
}
