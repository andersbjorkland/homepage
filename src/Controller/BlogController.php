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

                if (null !== $request->request->get('publishTime')) {
                    $publish = $request->request->get('publishTime');
                    $post->setPublishTime(new DateTime($publish));
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
}
