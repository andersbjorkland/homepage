<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Run;
use App\Entity\RunType;
use App\Form\RunFormType;

class RunningController extends AbstractController {

    /**
     * @Route("/admin/running", name="app_running")
     */
    public function index() {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/admin/running/add", name="app_running_add")
     */
    public function add(Request $request) {
        $run = new Run();
        $form = $this->createForm(RunFormType::class, $run)
            ->add('save', SubmitType::class, ['label' => 'Add run']);
        $form->handleRequest($request);

        $errors = array();
        $added = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $runType = $form->get('type')->getData();
                $runTypeRepository = $this->getDoctrine()->getRepository(RunType::class);
                $storedRunType = $runTypeRepository->findOneByName($runType->getName() );
                if (!$storedRunType) {
                    $entityManager->persist($runType);
                } else {
                    $runType = $storedRunType;
                }
                $run->setType($runType);
            } catch (RuntimeException $e) {
                $errors[] = $e;
            }

            try {
                $runDate = $form->get('date')->getData();
                $weightPre = $form->get('weightPre')->getData();
                $weightPost = $form->get('weightPost')->getData();
                $weightDiff = $weightPre - $weightPost;
                $distance = $form->get('distance')->getData();
                $time = $form->get('time')->getData();

                $run->setDate($runDate);
                $run->setWeightPre($weightPre);
                $run->setWeightPost($weightPost);
                $run->setWeightDiff($weightDiff);
                $run->setDistance($distance);
                $run->setTime($time);

                $entityManager->persist($run);
            } catch (RuntimeException $e) {
                $errors[] = $e;
            }
            

            if (empty($errors)) {
                $entityManager->flush();
                $added = true;
            } 

            
        }

        return $this->render('running/add.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
            'added' => $added
        ]);
    }

}
