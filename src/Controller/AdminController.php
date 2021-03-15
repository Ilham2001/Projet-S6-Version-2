<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Evaluation;
use App\Entity\Question;
use App\Entity\Thematique;
use App\Entity\TypeQuestion;
use App\Form\UserFormType;
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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminController extends AbstractController
{

    public function index()
    {
        return $this->render('/admin/index.html.twig');
    }

    public function users_list(Request $request)
    {
        /* Filtre par login d'utilisateur */
        $login_form = $this->createFormBuilder(null)
            ->add('login',TextType::class, array('attr' => [
                'required' => false,
                'placeholder' => 'Nom d\'utilisateur']))
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN'
                ],
                'label' => 'Rôles' 
            ])
            ->add('rechercher',SubmitType::class,array('label' => 'Rechercher'))
            ->getForm();
        $login_form->handleRequest($request);
        if($login_form->isSubmitted() && $login_form->isValid()) {
            
            $login = $login_form['login']->getData();
            $role = $login_form['roles']->getData();
            $users = $this->getDoctrine()->getRepository(User::class)->findByLogin($login,$role);
            
            return $this->render('/admin/users.html.twig', array(
                'login_form' => $login_form->createView(),
                'users' => $users));
        }

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('/admin/users.html.twig', array(
            'login_form' => $login_form->createView(),
            'users' => $users));
    }
    /* Ajouter un nouvel utilisateur */
    public function ajouter_user(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
        $user = new User;
        //Formulaire
        $ajouter_user_form = $this->createForm(UserFormType::class, $user);
        $ajouter_user_form->handleRequest($request);

        if ($ajouter_user_form->isSubmitted() && $ajouter_user_form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $ajouter_user_form->get('password')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            
            return $this->redirectToRoute('admin_users');
        }
        return $this->render('/admin/ajouterUser.html.twig',
           array('ajouter_user_form' => $ajouter_user_form->createView()));
    }
    /* Modifier un  utilisateur */
    public function modifier_user($id) {
        $user = new user;
        
        $user=$this->getDoctrine()->getRepository(User::class)->find($id);
        if(!$user) {
            throw $this->createNotFoundException('Utilisateur [id='.$id.'] inexistant.');
        }
        $modifier_user_form=$this->createForm(UserFormType::class,$user,
            ['action' => $this->generateUrl('admin_modifier_user_suite',
                array('id' => $user->getId()))]);


        return $this->render('/admin/modifierUser.html.twig',
           array('modifier_user_form' => $modifier_user_form->createView()));
    }
    /* Suite de la modification d'un utilisateur */
    public function modifier_user_suite(Request $request, $id,UserPasswordEncoderInterface $passwordEncoder ) {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        if(!$user) {
            throw $this->createNotFoundException('Utilisateur [id='.$id.'] inexistant.');
        }
        $modifier_user_form=$this->createForm(UserFormType::class,$user,
            ['action' => $this->generateUrl('admin_modifier_user_suite',
                array('id' => $user->getId()))]);
        $modifier_user_form->handleRequest($request);
        if($modifier_user_form->isSubmitted() && $modifier_user_form->isValid()){
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $modifier_user_form->get('password')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('admin_users');
        }
    }
    /* Supprimer un utilisateur */
    public function supprimer_user($id) {
        $user = new User;
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($id);
        $em->persist($user);
        $em->remove($user); 
        $em->flush();
        //add flash here
        return $this->redirectToRoute('admin_users');
    }
    /* Liste des evaluations */
    public function evaluations_list(UserInterface $user)
    {
        $user = $this->getUser();
        /* Find evaluations by user */
        $user_evaluations = $this->getDoctrine()->getRepository(Evaluation::class)->findByUser($user);

        $evaluations = $this->getDoctrine()->getRepository(Evaluation::class)->findAll();
        return $this->render('/admin/evaluations.html.twig', array (
            'userEvaluations' => $user_evaluations,
            'evaluations' => $evaluations
        ));
    }
    /* Générer une évaluation */
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

        $type = $this->getDoctrine()->getRepository(TypeQuestion::class)->find(1);
        $thema = $this->getDoctrine()->getRepository(Thematique::class)->find(2);
        $question = new Question;
        $propos = ["propo1", "propo2", "propo3"];
        $reponses = ["propo1","propo3"];
        $question->setContenuQuestion("Question 1");
        $question->setPropositionsQuestion($propos);
        $question->setReponsesQuestion($reponses);
        $question->setTypeQuestion($type);
        $question->setThematiqueQuestion($thema);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($question);
        $entityManager->flush();
        dd('done');

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