<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use \RuntimeException;

class RegisterController extends AbstractController {

    /**
     * @Route("/register", name="app_register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder) {
        $user = new User();
        $form = $this->createForm(UserType::class, $user)
            ->add('Register', SubmitType::class);

        $errors = array();
        $added = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $user->setEmail($form->get("email")->getData());
                $password = $form->get("password")->getData();
                $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            } catch (RuntimeException $e) {
                $errors[] = $e;
            }

            if (empty($errors)) {
                $entityManager->persist($user);
                $entityManager->flush();
                $added = true;
            }
        }

        return $this->render('register.html.twig', [
            'form' => $form->createView(),
            'added' => $added,
            'errors' => $errors
        ]);
    }

}
