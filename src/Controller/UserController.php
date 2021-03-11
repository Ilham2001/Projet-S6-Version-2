<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    public function questions_list(Request $request)
    {
        $filtre_question_form = $this->createFormBuilder(null)
            ->add('question',TextType::class, array('label' => false, 'attr' => array('placeholder' => 'Rechercher une question...')))
            ->add('type',ChoiceType::class , array('label' => false, 'choices' => [ 'QCM' => null, 'Frai ou faux' => null, 'Item3' => null]))
            ->add('thematique',ChoiceType::class,array('label' => false, 'choices' => [ 'UML' => null, 'Item2' => null, 'Item3' => null]))
            ->add('matiere',ChoiceType::class,array('label' => false, 'choices' => [ 'Item1' => null, 'Item2' => null, 'Item3' => null]))
            ->getForm();
        $filtre_question_form->handleRequest($request);
        if($filtre_question_form->isSubmitted() && $filtre_question_form->isValid()) {
            dd("filtre quesiton");
        }
        return $this->render('/user/questions.html.twig',
           array('filtre_question_form' => $filtre_question_form->createView()));
    }
}
