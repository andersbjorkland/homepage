<?php

namespace App\Controller;

use App\Entity\BlogImage;
use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\Image;
use App\Form\BlogPostType;
use App\Form\BlogUpdateType;
use App\Repository\BlogPostRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use function ceil;



class BlogController extends AbstractController
{
    /**
     * @Route("/blog/", name="post_index")
     */
    public function index() {
        return $this->indexByPage(1);
    }

    /**
     * @Route("/admin/blog/add", name="post_add", methods={"GET", "POST"})
     */
    public function addPost(Request $request, SluggerInterface $slugger)
    {
        $blogPost = new BlogPost();
        $added = false;

        $form = $this->createForm(BlogPostType::class, $blogPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entitymanager = $this->getDoctrine()->getManager();

            foreach ($form->get('categories') as $category) {
                $categoryName = $category->get('name')->getData();
                $categoryEntity = $entitymanager->getRepository(Category::class)->findOneByName($categoryName);
                if (null === $categoryEntity) {
                    $categoryEntity = new Category();
                }
                $categoryEntity->setName($categoryName);

                $entitymanager->persist($categoryEntity);

                $blogPost->addCategory($categoryEntity);
            }

            foreach ($form->get('blogImages') as $blogImage) {
                $blogImageEntity = new BlogImage();

                $imageFile = $blogImage->get('image')->getData();
                $alt = $blogImage->get('alt')->getData();
                $subtext = $blogImage->get('subtext')->getData();

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
                $image->setAlt($alt);
                $entitymanager->persist($image);

                if (!empty($subtext)) {
                    $blogImageEntity->setSubtext($subtext);
                }

                $blogImageEntity->setImage($image);
                $blogImageEntity->setBlogPost($blogPost);
                $entitymanager->persist($blogImageEntity);

                $blogPost->addBlogImage($blogImageEntity);

            }

            $blogPost->setEntered(new DateTime('now'));
            $publishTime = $form->get('publishTime')->getData();
            if ($publishTime) {
                $blogPost->setPublishTime($publishTime);
            }
            $blogPost->updateSlug();

            foreach ($blogPost->getCategories() as $category) {
                $category->addBlogPost($blogPost);
                $entitymanager->persist($category);
            }
            $entitymanager->persist($blogPost);
            $entitymanager->flush();


            $added = true;

        }
        return $this->render('blog/new.html.twig', [
            'form' => $form->createView(),
            'added' => $added
        ]);
    }

    /**
     * @Route("/blog/{slug}", name="post_show")
     */
    public function show(BlogPost $blogPost, BlogPostRepository $postRepository)
    {
        $numberOfPosts = $postRepository->getCount();
        $limit = 10;
        $numberOfPages = ceil($numberOfPosts / $limit);
        $posts = $postRepository->getLatestPaginated(1, $limit);

        $text = $blogPost->getText();
        $blogImages = $blogPost->getBlogImages();
        $imageIndex = 1;
        foreach ($blogImages as $blogImage) {
            $pattern = '/\|' . $imageIndex . '\|/';
            if (strpos($text, '|' . $imageIndex . '|')) {
                $replace = '<div class="img-container"> <img src="/uploads/images/'
                    . $blogImage->getImage()->getFileName()
                    . '" alt="' . $blogImage->getImage()->getAlt() . '" /><p class="image-text">'. $blogImage->getSubtext() .'</p></div>';
                $text = preg_replace($pattern, $replace, $text);
            }
            $imageIndex++;
        }
        $image = null;
        if (count($blogImages) > 0) {
            $image = $blogImages[0]->getImage();
        }

        return $this->render('blog/view.html.twig', [
            "post" => $blogPost,
            "image" => $image,
            "title" => $blogPost->getTitle(),
            "publish_date" => $blogPost->getPublishTime(),
            "text" => $text,
            "blogPost" => $blogPost,
            'posts' => $posts,
            'pages' => $numberOfPages
        ]);

    }

    /**
     * @Route("/admin/blog/{slug}/edit", name="post_edit", methods={"GET", "POST"})
     */
    public function editPost(BlogPost $blogPost, Request $request, SluggerInterface $slugger)
    {
        $messages = [];

        $blogImages = $blogPost->getBlogImages();
        //used when lazy loading.
        foreach ($blogImages as $blogImage) {
            $image = $blogImage->getImage();
        }

        $form = $this->createForm(BlogUpdateType::class, $blogPost);
        $form->handleRequest($request);

        $filePath = $_SERVER['APP_ENV'] === 'dev' ? 'uploads/images' : $this->getParameter('images_directory');

        if ($form->isSubmitted() && $form->isValid()) {
            $entitymanager = $this->getDoctrine()->getManager();

            $prevCategories = $blogPost->getCategories();
            $currentCategories = $form->get('categories');

            // Remove categories no longer present
            foreach ($prevCategories as $prevCategory) {
                $remove = true;
                foreach ($currentCategories as $currentCategory) {
                    if ($currentCategory->get('name')->getData() === $prevCategory->getName()) {
                        $remove = false;
                    }
                }

                if ($remove) {
                    $blogPost->removeCategory($prevCategory);
                    $prevCategory->removeBlogPost($blogPost);
                    $entitymanager->persist($prevCategory);
                    $entitymanager->persist($blogPost);
                }
            }

            // Add any new categories
            foreach ($form->get('categories') as $category) {
                $add = true;

                foreach ($blogPost->getCategories() as $prevCategory) {
                    if ($category->get('name')->getData() === $prevCategory->getName()) {
                        $add = false;
                    }
                }
                if ($add) {
                    $categoryName = $category->get('name')->getData();
                    $categoryEntity = $entitymanager->getRepository(Category::class)->findOneByName($categoryName);

                    if (null === $categoryEntity) {
                        $categoryEntity = new Category();
                    }

                    $categoryEntity->setName($categoryName);
                    $categoryEntity->addBlogPost($blogPost);
                    $entitymanager->persist($categoryEntity);
                    $blogPost->addCategory($categoryEntity);
                }
            }


            $blogInputs = $request->request->get("blog_update");
            $hasBlogImages = array_key_exists("blogImages", $blogInputs);
            if ($hasBlogImages) {
                $blogInputs = $blogInputs["blogImages"];
            }
            $currentBlogImages = $form->get('blogImages');
            $prevBlogImages = $blogPost->getBlogImages();

            // Update or remove images
            foreach ($prevBlogImages as $prevBlogImage) {
                $remove = true;
                foreach ($blogInputs as $currentBlogImage) {
                    if ($hasBlogImages && $prevBlogImage->getImage()->getFileName() === $currentBlogImage['image']) {
                        $remove = false;

                        // check if something needs updating
                        $alt = $currentBlogImage['alt'];
                        $subtext = $currentBlogImage['subtext'];

                        $prevBlogImage->getImage()->setAlt($alt);
                        $prevBlogImage->setSubtext($subtext);
                        $entitymanager->persist($prevBlogImage);
                        break;
                    }
                }

                if ($remove) {
                    $deleteStatus = unlink($filePath . '/' . $prevBlogImage->getImage()->getFileName());
                    if ($deleteStatus) {
                        $blogPost->removeBlogImage($prevBlogImage);
                        $entitymanager->remove($prevBlogImage->getImage());
                        $entitymanager->remove($prevBlogImage);
                    } else {
                        $messages[] = "Could not remove file " . $prevBlogImage->getImage()->getFileName();
                    }
                }
            }

            // insert new images
            foreach ($currentBlogImages as $currentBlogImage) {
                $isNew = true;
                $currentFile = $currentBlogImage->get('image')->getData();

                foreach ($prevBlogImages as $prevBlogImage) {
                    if ($currentFile === $prevBlogImage->getImage()->getFileName()) {
                        $isNew = false;
                        break;
                    }
                }

                if ($isNew && !empty($currentFile)) {
                    $blogImageEntity = new BlogImage();
                    $alt = $currentBlogImage->get('alt')->getData();
                    $subtext = $currentBlogImage->get('subtext')->getData();

                    $originalFilename = pathinfo($currentFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$currentFile->guessExtension();

                    try {
                        $currentFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                        $errors[] = $e;
                    }

                    $image = new Image();
                    $image->setFileName($newFilename);
                    $image->setAlt($alt);
                    $entitymanager->persist($image);

                    if (!empty($subtext)) {
                        $blogImageEntity->setSubtext($subtext);
                    }

                    $blogImageEntity->setImage($image);
                    $blogImageEntity->setBlogPost($blogPost);
                    $entitymanager->persist($blogImageEntity);

                    $blogPost->addBlogImage($blogImageEntity);
                }
            }

            $entitymanager->flush();
        }

        return $this->render('blog/edit.html.twig', [
            "form" => $form->createView(),
            "blogPost" => $blogPost,
            "messages" => $messages,
            "filePath" => $filePath
        ]);
    }

    /**
     * @Route("/blog/page/{page}", name="post_page")
     */
    public function indexByPage($page) {
        $postRepository = $this->getDoctrine()->getRepository(BlogPost::class);
        $post = $postRepository->getLatestPost();

        $limit = 10;
        $numberOfPosts = $postRepository->getCount();
        $numberOfPages = ceil($numberOfPosts / $limit);
        if ($page > $numberOfPages) {
            $this->redirectToRoute("post_index");
        }

        $posts = $postRepository->getLatestPaginated($page, $limit);

        $image = null;
        if (null !== $post && null !== $post->getBlogImages()) {
            $image = $post->getBlogImages()[0]->getImage();
        }

        $text = $post->getText();
        $blogImages = $post->getBlogImages();
        $imageIndex = 1;
        foreach ($blogImages as $blogImage) {
            $pattern = '/\|' . $imageIndex . '\|/';
            if (strpos($text, '|' . $imageIndex . '|')) {
                $replace = '<div class="img-container"> <img src="/uploads/images/'
                    . $blogImage->getImage()->getFileName()
                    . '" alt="' . $blogImage->getImage()->getAlt() . '" /><p class="image-text">'. $blogImage->getSubtext() .'</p></div>';
                $text = preg_replace($pattern, $replace, $text);
            }
            $imageIndex++;
        }

        return $this->render('blog/index.html.twig', [
            'post' => $post,
            'text' => $text,
            'posts' => $posts,
            'image' => $image,
            'pages' => $numberOfPages,
            'currentPage' => $page
        ]);
    }

    /**
     * @Route("/admin/blog/delete/{id}", name="post_delete")
     */
    public function delete(Request $request, BlogPost $post) {
        $removed = false;

        if ("POST" === $request->getMethod()) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($post->getBlogImages() as $blogImage) {

                $post->removeBlogImage($blogImage);
                $this->deleteImageFile($blogImage->getImage());
                $entityManager->remove($blogImage->getImage());
                $entityManager->remove($blogImage);
            }
            $entityManager->remove($post);
            $entityManager->flush();
            $removed = true;
        }

        return $this->render('blog/delete.html.twig', [
            'post' => $post,
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
