<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Evaluation;
use App\Entity\Question;
use App\Entity\Matiere;
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
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AdminController extends AbstractController
{

    public function index()
    {
        return $this->render('/admin/index.html.twig');
    }

    public function users_list(Request $request) {
        /* Filtre par login d'utilisateur */
        $login_form = $this->createFormBuilder(null)
            ->add('rechercheData',TextType::class, array('attr' => [
                'placeholder' => 'Rechercher un utilisateur ...']))
            ->add('rechercher',SubmitType::class,array('label' => 'Rechercher'))
            ->getForm();
        $login_form->handleRequest($request);
        if($login_form->isSubmitted() && $login_form->isValid()) {
            
            $rechercheData = $login_form['rechercheData']->getData();
            //$role = $login_form['roles']->getData();
            $users = $this->getDoctrine()->getRepository(User::class)->findByLoginorRole($rechercheData);
            
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
    public function questions_list(Request $request) {
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
        $questions = $this->getDoctrine()->getRepository(Question::class)->findAll();
        return $this->render('/admin/questions.html.twig', array(
            'filtre_question_form' => $filtre_question_form->createView(),
            'questions' => $questions));
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

        $type = $this->getDoctrine()->getRepository(TypeQuestion::class)->find(4);
        $thematique = $this->getDoctrine()->getRepository(Thematique::class)->find(1); //culture generale
        $question = new Question;
        $propos = ["Bonne réponse1", "Bonne réponse2", "Bonne réponse3", "Bonne réponse4", "Mauvaise réponse"];
        $reponses = ["Bonne réponse1", "Bonne réponse2", "Bonne réponse3", "Bonne réponse4"];
        $question->setContenuQuestion("Quelle est a réponse à cette question à choix multiples ?");
        $question->setPropositionsQuestion($propos);
        $question->setReponsesQuestion($reponses);
        $question->setTypeQuestion($type);
        $question->setThematiqueQuestion($thematique);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($question);
        $entityManager->flush();
        dd('done');

        return $this->render('/admin/ajouterQuestion.html.twig',
           array('ajouter_question_form' => $ajouter_question_form->createView()));
    }

    /* Supprimer question */
    public function supprimer_question($id) {
        $question = new Question;
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Question::class);
        $question = $repo->find($id);
        $em->persist($question);
        $em->remove($question); 
        $em->flush();
        //add flash here
        return $this->redirectToRoute('admin_questions');
    }

    /* Liste thématiques et matières */
    public function thematiques_matieres(Request $request) {
        $thematiques= new Thematique;
        $matieres= new Matiere;
        $form = $this->createFormBuilder(null)
                ->add('titre', TextType::class)
                ->add('submit', SubmitType::class, array('label' => 'Rechercher', 'attr' => ['class' => 'btn btn-primary']  ))
                ->getForm();

        $thematiques=$this->getDoctrine()->getRepository(Thematique::class)->findAll();
        $matieres=$this->getDoctrine()->getRepository(Matiere::class)->findAll();

        return $this->render('admin/thematiquesMatieres.html.twig',  
            array( 'thematiques' => $thematiques,
            'matieres'=>$matieres,
            'myform' => $form->createView() ));
    }
    /* Supprimer thématique */ 
    public function Supprimer($id) {
        $em=$this->getDoctrine()->getManager();
        $res=$em->getRepository(Thematique::class);
        $Thematique=$res->find($id);
        $em->remove($Thematique);
        $em->flush();
        $this->addFlash('danger', 'Thematique a été bien supprimé');
        return $this->redirectToRoute('admin_thematiques_matieres',["Thematique"=>$Thematique]);
    }

    /* Supprimer matière */
    public function supprimer_matiere($id) {
        $en=$this->getDoctrine()->getManager();
        $rest=$en->getRepository(Matiere::class);
        $Matiere=$rest->find($id);
        $en->remove($Matiere);
        $en->flush();
        $this->addFlash('danger', 'Matiere a été bien supprimé');
        return $this->redirectToRoute('admin_thematiques_matieres',["Matiere"=>$Matiere]);
    }
    /* Modifier thématique */
    public function modifier_thematique($id,Request $request) {
        $thematique=$this->getDoctrine()->getRepository(Thematique::class)->find($id);
        $form=$this->createForm(ThematiqueType::class,$thematique);

        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
    
            if ($form->isSubmitted() && $form->isValid()) {
                
    
                $em=$this->getDoctrine()->getManager();
                $em->flush();
                // perform some action...
                $this->addFlash('success', 'Thématique a été bien modifié');
                return $this->redirectToRoute('admin_thematiques_matieres');
            }
        }
        return $this->render('admin/modifierThematique.html.twig', [
            
            'form'  =>  $form->createView(),
        ]);
    }

    /* Modifier matière */
    public function modifier_matiere($id,Request $request) {
        $matiere=$this->getDoctrine()->getRepository(Matiere::class)->find($id);
        $form_matiere=$this->createForm(MatiereType::class,$matiere);

        if ($request->isMethod('POST')) {
            $form_matiere->submit($request->request->get($form_matiere->getName()));
    
            if ($form_matiere->isSubmitted() && $form_matiere->isValid()) {
                
    
                $em=$this->getDoctrine()->getManager();
                $em->flush();
                // perform some action...
                $this->addFlash('success', 'Matière a été bien modifié');
                return $this->redirectToRoute('admin_thematiques_matieres');
            }
        }
        return $this->render('admin/modifierMatiere.html.twig', [
            
            'form_matiere'  =>  $form_matiere->createView(),
        ]);
    }
    /* Ajouter thématique ou matière */

    public function ajouter_categorie(Request $request) {
        $thematique = new Thematique;
            $matiere = new Matiere;
            $choix = $request->request->get("choix");
            $Thematiqu = $request->request->get("Thematique");
            $Matier = $request->request->get("Matiere");
            $em = $this->getDoctrine()->getManager();

        if($choix == 'thema') {
            $thematique->setLibelleThematique($Thematiqu);
            $em->persist($thematique);
            $em->flush();
        }

        if($choix == 'matiere') {
            $matiere->setLibelleMatiere($Matier);
            $em->persist($matiere);
            $em->flush();
            }
        $message="Bien ajoutee ";
        return $this->render('/admin/ajouterCategorie.html.twig',array('msg'=>$message));
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
    /* Supprimer évaluation */
    public function supprimer_evaluation($id) {
        $evaluation = new Evaluation;
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Evaluation::class);
        $evaluation = $repo->find($id);
        $em->persist($evaluation);
        $em->remove($evaluation); 
        $em->flush();
        //add flash here
        return $this->redirectToRoute('admin_evaluations');
    }
    /* Afficher évaluation */
    public function afficher_evaluation($id) {
        $evaluation = new Evaluation;
        $evaluation = $this->getDoctrine()->getRepository(Evaluation::class)->find($id);
        return $this->render('/admin/afficherEvaluation.html.twig', array (
            'evaluation' => $evaluation
        ));
    }

    /* Générer une évaluation */
    public function generer_evaluation(Request $request) {

        $questions = $this->getDoctrine()->getRepository(Question::class)->findAll();
        return $this->render('/admin/genererEvaluation.html.twig', array(
            'questions' => $questions));
    }
    
    /* Suite de la génération (création des fichiers) */
    public function generer_evaluation_suite() {
        $evaluation = new Evaluation;
 
        if(isset($_POST['checkBox']) && !empty($_POST['checkBox'])){

            $nom_evaluation = $_POST['nom_evaluation'];
            $ids_of_selected_questions = $_POST['checkBox'];
            $selected_questions = $this->getDoctrine()->getRepository(Question::class)->findById($ids_of_selected_questions);
  
            $user = $this->getUser();
            //dd($user);
            foreach($selected_questions as $question) {
                $evaluation->addQuestion($question);
            }
            //dd($evaluation);
            $evaluation->setDateEvaluation(new \DateTime('@'.strtotime('now')));
            $evaluation->setUser($user);
            $evaluation->setNomEvaluation($nom_evaluation);
            $evaluation->setContenuFichier("");

            $questions = $evaluation->getQuestions();
            /* Créer fichier */
            $fichier = new Filesystem();
            $chemin_courant = getcwd();
    
            foreach($questions as $question) {
                $type_question = $question->getTypeQuestion();
                /* Question de type Vrai ou Faux */
                if($type_question->getId() == 1) {
                    $reponses = $question->getReponsesQuestion();
                    if($reponses[0] == "Vrai") {
    
                        try {
                            $chemin_fichier = $chemin_courant . "/fichiers/". $evaluation->getNomEvaluation() .".txt";
                            if(!$fichier->exists($chemin_fichier)) {
                                $fichier->touch($chemin_fichier);
                                $fichier->chmod($chemin_fichier,0777);
                                $fichier->dumpFile($chemin_fichier, $question->getContenuQuestion()." {T}\n");
                            }
                            else{
                                $fichier->appendToFile($chemin_fichier,"\n".$question->getContenuQuestion()." {T}\n");
                            }
                        } catch(IOExceptionInterface $exception) {
                            echo "Error creating file at". $exception->getPath();
                        }
                    }
                    elseif($reponses[0] == "Faux") {
                        try {
                            $chemin_fichier = $chemin_courant . "/fichiers/". $evaluation->getNomEvaluation() .".txt";
                            if(!$fichier->exists($chemin_fichier)) {
                                $fichier->touch($chemin_fichier);
                                $fichier->chmod($chemin_fichier,0777);
                                $fichier->dumpFile($chemin_fichier, $question->getContenuQuestion()." {F}\n");
                            }
                            else{
                                $fichier->appendToFile($chemin_fichier,"\n".$question->getContenuQuestion()." {F}\n");
                            }
                        } catch(IOExceptionInterface $exception) {
                            echo "Error creating file at". $exception->getPath();
                        }
                    }
                } 
                /* Question de type réponse libre */
                elseif($type_question->getId() == 2) {
                    try {
                        $chemin_fichier = $chemin_courant . "/fichiers/". $evaluation->getNomEvaluation() .".txt";
                        if(!$fichier->exists($chemin_fichier)) {
                            $fichier->touch($chemin_fichier);
                            $fichier->chmod($chemin_fichier,0777);
                            $fichier->dumpFile($chemin_fichier, $question->getContenuQuestion() . " {}\n");
                        }
                        else{
                            $fichier->appendToFile($chemin_fichier, "\n".$question->getContenuQuestion() . " {}\n");
                        }
                    } catch(IOExceptionInterface $exception) {
                        echo "Error creating file at". $exception->getPath();
                    }
                }
                /* Question de type choix unique */
                elseif($type_question->getId() == 3) {
                    $reponses = $question->getReponsesQuestion();
                    $propos = $question->getPropositionsQuestion();
                    //dd($propos);
                    try {
                        $chemin_fichier = $chemin_courant . "/fichiers/". $evaluation->getNomEvaluation() .".txt";
                        if(!$fichier->exists($chemin_fichier)) {
                            $fichier->touch($chemin_fichier);
                            $fichier->chmod($chemin_fichier,0777);
                            $fichier->dumpFile($chemin_fichier, $question->getContenuQuestion() . " ");
                            $fichier->appendToFile($chemin_fichier," {=".$reponses[0]);
                            foreach($propos as $propo) {
                                if($propo!=$reponses[0])
                                $fichier->appendToFile($chemin_fichier, " ~" . $propo );
                            }
                            $fichier->appendToFile($chemin_fichier, "}\n");
                        }
                        
                        else{
                            //dd($propos);
                            $fichier->appendToFile($chemin_fichier, "\n".$question->getContenuQuestion()."{=".$reponses[0]);
                            foreach($propos as $propo) {
                                if($propo!=$reponses[0])
                                    $fichier->appendToFile($chemin_fichier, " ~" . $propo );
                                }
                            $fichier->appendToFile($chemin_fichier, "}\n");
                        }
                    } catch(IOExceptionInterface $exception) {
                        echo "Error creating file at". $exception->getPath();
                    }
                }
                /* Réponse de type choix multiple */
                elseif ($type_question->getId() == 4) {
                    $reponses = $question->getReponsesQuestion();
                    $propos = $question->getPropositionsQuestion();
                    
                    try {
                        $chemin_fichier = $chemin_courant . "/fichiers/". $evaluation->getNomEvaluation() .".txt";
                        /* If file doesn't exist */
                        if(!$fichier->exists($chemin_fichier)) {
                            $fichier->touch($chemin_fichier);
                            $fichier->chmod($chemin_fichier,0777);
                            $fichier->dumpFile($chemin_fichier, $question->getContenuQuestion() . " {\n");
                            /* S'il y a 1 réponse correcte */
                            if(sizeof($reponses) == 1) {
                                foreach($reponses as $reponse) {
                                    $fichier->appendToFile($chemin_fichier, "~%100%".$reponse."\n");
                                }
                            }
                            /* 2 réponses correctes */
                            elseif(sizeof($reponses) == 2) {
                                foreach($reponses as $reponse) {
                                    $fichier->appendToFile($chemin_fichier, "~%50%".$reponse."\n");
                                }
                            }
                            /* 3 réponses correctes */
                            elseif(sizeof($reponses) == 3) {
                                foreach($reponses as $reponse) {
                                    $fichier->appendToFile($chemin_fichier, "~%33.33333%".$reponse."\n");
                                }
                            }
                            /* 4 réponses correctes */
                            elseif(sizeof($reponses) == 4) { 
                                foreach($reponses as $reponse) {
                                    $fichier->appendToFile($chemin_fichier, "~%25%".$reponse."\n");
                                }
                            }
                           
                            foreach($propos as $propo) {
                                if(!in_array($propo,$reponses)) {
                                    $fichier->appendToFile($chemin_fichier, "~%0%".$propo."\n");
                                }
                            }
                            $fichier->appendToFile($chemin_fichier, "}\n");
                        }
                        /* If file already exists */
                        else{
                            $fichier->appendToFile($chemin_fichier, "\n".$question->getContenuQuestion()." {\n");
                            /* S'il y a 1 réponse correcte */
                            if(sizeof($reponses) == 1) {
                                foreach($reponses as $reponse) {
                                    $fichier->appendToFile($chemin_fichier, "~%100%".$reponse."\n");
                                }
                            }
                            /* S'il y a 2 réponses correctes */  
                            elseif(sizeof($reponses) == 2) {
                                foreach($reponses as $reponse) {
                                    $fichier->appendToFile($chemin_fichier, "~%50%".$reponse."\n");
                                }
                            }
                            /* 3 réponses correctes */
                            elseif(sizeof($reponses) == 3) {
                                foreach($reponses as $reponse) {
                                    $fichier->appendToFile($chemin_fichier, "~%33.33333%".$reponse."\n");
                                }
                            }
                            /* 4 réponses correctes */
                            elseif(sizeof($reponses) == 4) { 
                                foreach($reponses as $reponse) {
                                    $fichier->appendToFile($chemin_fichier, "~%25%".$reponse."\n");
                                }
                            }
                            foreach($propos as $propo) {
                                if(!in_array($propo,$reponses)) {
                                    $fichier->appendToFile($chemin_fichier, "~%0%".$propo."\n");
                                }
                            }
                            $fichier->appendToFile($chemin_fichier, "}\n");
                            
                        }
                    } catch(IOExceptionInterface $exception) {
                        echo "Error creating file at". $exception->getPath();
                    }
                }
                /* Question de type réponse numérique */
                elseif ($type_question->getId() == 5) {
                    $reponses = $question->getReponsesQuestion();
                    try {
                        $chemin_fichier = $chemin_courant . "/fichiers/". $evaluation->getNomEvaluation() .".txt";
                        if(!$fichier->exists($chemin_fichier)) {
                            $fichier->touch($chemin_fichier);
                            $fichier->chmod($chemin_fichier,0777);
                            $fichier->dumpFile($chemin_fichier, $question->getContenuQuestion() . " {#". $reponses[0]."}\n");
                        }
                        
                        else{
                            $fichier->appendToFile($chemin_fichier, "\n".$question->getContenuQuestion() . " {#". $reponses[0]."}\n");
                        }
                    } catch(IOExceptionInterface $exception) {
                        echo "Error creating file at". $exception->getPath();
                    }
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evaluation);
            $entityManager->flush();
        }
        return $this->render('/admin/genererFichierEvaluation.html.twig', array(
            'evaluation' => $evaluation));
    }

    /* Générer le fichier */
    public function generer_fichier(Request $request, $id) {
        
        $evaluation = $this->getDoctrine()->getRepository(Evaluation::class)->find($id);
        $publicDir = $this->getParameter('kernel.project_dir') . '/public/';
        $response = new BinaryFileResponse($publicDir .'/fichiers/'. $evaluation->getNomEvaluation().'.txt');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $evaluation->getNomEvaluation().'.txt');
        return $response;
    }

}