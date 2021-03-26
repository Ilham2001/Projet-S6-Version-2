<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Evaluation;
use App\Entity\Question;
use App\Entity\Matiere;
use App\Entity\Thematique;
use App\Entity\TypeQuestion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    /* Liste des questions */
    public function questions_list(Request $request)
    {
        $questions = $this->getDoctrine()->getRepository(Question::class)->findAll();
        $types_question = $this->getDoctrine()->getRepository(TypeQuestion::class)->findAll();
        $thematiques_question = $this->getDoctrine()->getRepository(Thematique::class)->findAll();
        $matieres_question = $this->getDoctrine()->getRepository(Matiere::class)->findAll();

        if( isset($_POST['type_question']) || isset($_POST['matiere_question']) || isset($_POST['thematique_question']) ) {

            $type = $this->getDoctrine()->getRepository(TypeQuestion::class)->find($_POST['type_question']);
            $matiere = $this->getDoctrine()->getRepository(Matiere::class)->find($_POST['matiere_question']);
            $thematique = $this->getDoctrine()->getRepository(Thematique::class)->find($_POST['thematique_question']);
            $questions = $this->getDoctrine()->getRepository(Question::class)->findByTypeOrThematiqueOrMatiere($type,$thematique,$matiere);
            return $this->render('/user/questions.html.twig', array(
                'questions' => $questions,
                'types_question' => $types_question,
                'thematiques_question' => $thematiques_question,
                'matieres_question' => $matieres_question
            ));
        }

        return $this->render('/user/questions.html.twig', array(
            'questions' => $questions,
            'types_question' => $types_question,
            'thematiques_question' => $thematiques_question,
            'matieres_question' => $matieres_question
        ));
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
    
        return $this->render('/user/thematiquesMatieres.html.twig',  
             array( 'thematiques' => $thematiques,
            'matieres'=>$matieres,
            'myform' => $form->createView() ));
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
        return $this->render('/user/ajouterCategorie.html.twig',array('msg'=>$message));
    }
    /* Liste des evaluations */
    public function evaluations_list(UserInterface $user)
    {
        $user = $this->getUser();
        /* Find evaluations by user */
        $evaluations = $this->getDoctrine()->getRepository(Evaluation::class)->findByUser($user);
    
        return $this->render('/user/evaluations.html.twig', array (
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
        return $this->redirectToRoute('user_evaluations');
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
        $types_question = $this->getDoctrine()->getRepository(TypeQuestion::class)->findAll();
        $thematiques_question = $this->getDoctrine()->getRepository(Thematique::class)->findAll();
        $matieres_question = $this->getDoctrine()->getRepository(Matiere::class)->findAll();

        if( isset($_POST['type_question']) || isset($_POST['matiere_question']) || isset($_POST['thematique_question']) ) {
            
            $type = $this->getDoctrine()->getRepository(TypeQuestion::class)->find($_POST['type_question']);
            $matiere = $this->getDoctrine()->getRepository(Matiere::class)->find($_POST['matiere_question']);
            $thematique = $this->getDoctrine()->getRepository(Thematique::class)->find($_POST['thematique_question']);
            $questions = $this->getDoctrine()->getRepository(Question::class)->findByTypeOrThematiqueOrMatiere($type,$thematique,$matiere);
            return $this->render('/user/genererEvaluation.html.twig', array(
                'questions' => $questions,
                'types_question' => $types_question,
                'thematiques_question' => $thematiques_question,
                'matieres_question' => $matieres_question
            ));
        }
        return $this->render('/user/genererEvaluation.html.twig', array(
            'questions' => $questions,
            'types_question' => $types_question,
            'thematiques_question' => $thematiques_question,
            'matieres_question' => $matieres_question
        ));
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
        return $this->render('/user/genererFichierEvaluation.html.twig', array(
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
