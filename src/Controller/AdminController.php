<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class AdminController extends AbstractController
{

    public function index()
    {
        return $this->render('/admin/index.html.twig');
    }

    public function users_list()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('/admin/users.html.twig', array('users' => $users));
    }

    public function ajouter_user(Request $request)
    {
        $ajouter_user_form = $this->createFormBuilder(null)
            ->add('login',TextType::class,array('label' => false, 'attr' => array('placeholder' => 'Nom d\'utilisateur')))
            ->add('password',PasswordType::class , array('label' => false, 'attr' => array('placeholder' => 'Mot de passe')))
            ->add('ajouter',SubmitType::class,array('label' => 'Ajouter'))
            ->getForm();
        $ajouter_user_form->handleRequest($request);
        if ($ajouter_user_form->isSubmitted() && $ajouter_user_form->isValid()) {
            dd("add user");
        }
        return $this->render('/admin/ajouterUser.html.twig',
           array('ajouter_user_form' => $ajouter_user_form->createView()));
    }
    public function modifier_user(Request $request) {
        $modifier_user_form = $this->createFormBuilder(null)
            ->add('login',TextType::class,array('label' => false, 'attr' => array('placeholder' => 'Nom d\'utilisateur')))
            ->add('password',PasswordType::class , array('label' => false, 'attr' => array('placeholder' => 'Mot de passe')))
            ->add('confirm_password',PasswordType::class , array('label' => false, 'attr' => array('placeholder' => 'Confirmer votre mot de passe')))
            ->add('enregistrer',SubmitType::class,array('label' => 'Enregistrer'))
            ->getForm();
        $modifier_user_form->handleRequest($request);
        if ($modifier_user_form->isSubmitted() && $modifier_user_form->isValid()) {
            dd("modifier user");
        }
        return $this->render('/admin/modifierUser.html.twig',
           array('modifier_user_form' => $modifier_user_form->createView()));
    }

    public function evaluations_list()
    {
        return $this->render('/admin/evaluations.html.twig');
    }

    public function generer_evaluation(Request $request) {
        $generer_evaluation_form = $this->createFormBuilder(null)
            ->add('evaluation_nom',TextType::class,array('label' => false))
            /*-> array of obejcts of selectionned questions*/
            ->add('ajouter',SubmitType::class,array('label' => 'Ajouter'))
            ->getForm();
        $generer_evaluation_form->handleRequest($request);
        if($generer_evaluation_form->isSubmitted() && $generer_evaluation_form->isValid()) {
            dd("generer");
        }
        return $this->render('/admin/genererEvaluation.html.twig');
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
        return $this->render('/admin/questions.html.twig',
           array('filtre_question_form' => $filtre_question_form->createView()));
    }

    public function ajouter_question(Request $request)
    {
        $ajouter_question_form = $this->createFormBuilder(null)
        ->add('question',TextareaType::class, array('label' => 'Question : ', 'attr' => array('placeholder' => 'Ecrire votre question...')))
        ->add('type',ChoiceType::class , array('label' => 'Type : ', 'choices' => [ 'QCM' => null, 'Frai ou faux' => null, 'Item3' => null]))
        ->add('propositions', TextType::class, array('label' => 'Propositions : '))
        ->add('autre_proposition',ButtonType::class, array('label' => '+'))
        ->add('thematique',ChoiceType::class,array('label' => 'Thématique : ', 'choices' => [ 'UML' => null, 'Item2' => null, 'Item3' => null]))
        ->add('matiere',ChoiceType::class,array('label' => 'Matière : ', 'choices' => [ 'Item1' => null, 'Item2' => null, 'Item3' => null]))
        ->add('autre_question',ButtonType::class, array('label' => '+ Autre Question'))
        ->add('ajouter',SubmitType::class, array('label' => 'Ajouter'))
        ->getForm();
        $ajouter_question_form->handleRequest($request);
        if($ajouter_question_form->isSubmitted() && $ajouter_question_form->isValid()) {
            dd('ajouter form');
        }
        return $this->render('/admin/ajouterQuestion.html.twig',
           array('ajouter_question_form' => $ajouter_question_form->createView()));
    }

    public function ajouter_categorie(Request $request)
    {
        $ajouter_categorie_form = $this->createFormBuilder(null)
        ->add('categorie', ChoiceType::class, array('label' => false, 'choices' => array(
                'Thématique' => 'thematique',
                'Matière' => 'matiere',),
                'expanded' => true))
        ->add('nom_categorie', TextType::class, array('label' => false, 'attr' => array('placeholder' => 'Ex : Anglais')))
        ->add('autre_categorie',ButtonType::class, array('label' => '+'))
        ->add('ajouter',SubmitType::class, array('label' => 'Ajouter'))
        ->getForm();
        $ajouter_categorie_form->handleRequest($request);
        if($ajouter_categorie_form->isSubmitted() && $ajouter_categorie_form->isValid()) {
            dd('ajouter form');
        }
        return $this->render('/admin/ajouterCategorie.html.twig',
           array('ajouter_categorie_form' => $ajouter_categorie_form->createView()));
    }
}