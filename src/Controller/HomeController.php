<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\Question;
use App\Form\ReponseType;
use App\Form\QuestionType;
use App\Entity\Proposition;
use App\Entity\Questionnaire;
use App\Form\PropositionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



class HomeController extends AbstractController
{
    /**
     * @Route("/home/{id<\d+>}/admin", name="home")
     */
    public function index(Request $request,Question $question = null,Questionnaire $questionnaire = null){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $questRepo = $this->getDoctrine()->getRepository(Questionnaire::class);
        $repo = $this->getDoctrine()->getRepository(Question::class);
        $questions = $repo->findBy(["questionnaire" => $questionnaire->getId()], ['title' => 'ASC']);
        $questionnaires = $questRepo->findAll();
        $name = $questionnaire->getName();
        
        $question = new Question();
        
        $form = $this->createForm(QuestionType::class, $question);
        if($questionnaire){
            $form->remove("questionnaire");           
            $question->setQuestionnaire($questionnaire);
        }
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();

            $this->addFlash('success', 'Question ajouté avec succès');
            return $this->redirectToRoute('home',array("id" => $questionnaire->getId()));
        }
        return $this->render(
            'home/index.html.twig', [
                "questions" => $questions,
                "form"      => $form->createView(),
                "name"      => $name
            ]
           
        );
    }

    /**
     * @Route ("/newProp/{id<\d+>}", name="newProp")
     * @Route("/{id<\d+>}", name="question")
     *
     */
    public function question(Request $request,Question $question,Proposition $proposition = null){
        // if(!$proposition){
            $proposition = new Proposition();
        // }
        // var_dump($request->get('proposition'));die;
        $form = $this->createForm(PropositionType::class, $proposition);
        if($question){
            $form->remove("question");           
            $proposition->setQuestion($question);
        }
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($proposition);
            $em->flush();

            $this->addFlash('success', 'proposition ajoutée avec succès');
            return $this->redirectToRoute('newProp',array("id" => $question->getId()));
            
        }
        
        return $this->render(
            'home/question.html.twig', [
                "question" => $question,
                "form"     => $form->createView()  
            ]
        );
    }
     /**
     *  @Route ("modifProp/{id<\d+>}", name="modifProp")
     */
    public function editProp(Proposition $proposition = null, Request $request){
        $propRepo = $this->getDoctrine()->getRepository(Proposition::class);
        $proposition = $propRepo->findOneBy(["id" => $request->get("id")]);
        // dump($proposition);die;
        if(!$proposition){
            $proposition = new Proposition();
        }
        
        $form = $this->createForm(PropositionType::class, $proposition);
        if($proposition){
            $form->remove("question");           
            $proposition->setQuestion($proposition->getQuestion());
        }
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($proposition);
            $em->flush();

            $this->addFlash('success', 'proposition modifée avec succès');
            return $this->redirectToRoute('newProp',array("id" => $proposition->getQuestion()->getId()));
        }
        return $this->render(
            'home/question.html.twig', [
                "question" => $proposition->getQuestion(),
                "form"     => $form->createView()  
            ]);
    }

    /**
     * @Route("/new", name="newQuestion")
     * @Route("/edit/{id}", name="editQuestion")
     */
    public function new(Question $question = null, Request $request){

        if(!$question){
            $question = new Question();
        }
        
        $form = $this->createForm(QuestionType::class, $question);
       
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();

            $this->addFlash('success', 'Question ajouté avec succès');
            return $this->redirectToRoute('questionnaire');
        }

        return $this->render(
            "home/new.html.twig", [
                "form" => $form->createView()
            ]
        );
    }

    /**
     * @Route("/affichage/profile", name="affich")
     */
    public function affich(Request $request,Question $question = null,Questionnaire $questionnaire = null){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $reponse = new Reponse();
        $questRepo = $this->getDoctrine()->getRepository(Questionnaire::class);
        $questionnaires = $questRepo->findAll();
        foreach ($questionnaires as $key => $questionnaire) {
            if(!$questionnaire->isPlayable($questionnaire)){
                array_splice($questionnaires,$key);

            }
        };
        return $this->render(
            'home/affich.html.twig', [
                "questionnaires" => $questionnaires,
            ]
           
        );
    }
    /**
     * @Route("/affich_quest/{id}", name="quest")
     */
    public function affichQuest(Request $request,Question $question = null,Questionnaire $questionnaire = null,ReponseType $builder){
        $form = $this->createForm(ReponseType::class,null,["data" => ["questionnaire" => $questionnaire]]);
   
       
        $form->handleRequest($request);
        $score = 0;
        if($form->isSubmitted() && $form->isValid()){
            $tabReponse = $request->request->all()["reponse"];
           
            unset($tabReponse["valider"]);
            unset($tabReponse["_token"]);
            if(!empty($tabReponse)){                
                $tab = [];
                $tab1 = [];
                $i = 0;
                $numQuest = "1";
                $nbrQuest = count($questionnaire->getQuestions());
                $valReponse = array_values($tabReponse);
                foreach ($tabReponse as $key => $value) {
                    $val = substr($key,-1);
                    
                    if($numQuest == $val){
                        if(!array_key_exists("question".$numQuest,$tab1)){
                            $tab1["question".$numQuest]["cocher"] = [];
                        }
                        array_push($tab1["question".$numQuest]["cocher"],$value);
                        array_push($tab,$value);                        
                    }
                    else{  
                        $numQuest = $val;
                        if(!array_key_exists("question".$numQuest,$tab1)){
                            $tab1["question".$numQuest]["cocher"] = [];
                        } 
                        array_push($tab1["question".$numQuest]["cocher"],$value);
                        array_push($tab,$value); 
                    }
                    $i++;
                }
               
                $i=1;
                foreach ($questionnaire->getQuestions() as $key => $value) {
                    $result =[];
                    $nbrProp = count($value->getPropositions());
                    if(array_key_exists("question".$i,$tab1) && count($tab1["question".($i)])<$nbrProp ){
                        $totalReponse = array_sum($tab1["question".($i)]["cocher"]);
                            $total = 0;
                            foreach ($value->getPropositions() as $key => $value) {
                                $total = $total + $value->getCorrect();
                            }
                            $tabProp = [];
                            $tab1["question".($i)]["results"] = [
                                "total_reponses" =>  array_sum($tab1["question".($i)]["cocher"]),"total_possible" => $total,"nbr_propositions" => $nbrProp
                            ];
                    }
                    else{
                        $this->addFlash("error","Vous devez répondre aux questions ?");
                        return $this->render(
                            'home/affichQuest.html.twig',[
                                "questionnaire" => $questionnaire,
                                "form"          =>$form->createView()
                            ]
                           
                        );
                    }
                    $i++;
                    
                }
                // dump($tab1);die;
                
                
                return $this->render(
                    'home/resultat.html.twig',[
                        "questionnaire" => $questionnaire,
                        "reponse"       =>$tab1,
                        "scorePhp"      => $score    
                    ]);
            }
            else{
                $this->addFlash("error","Vous devez répondre aux questions ?");
                return $this->render(
                    'home/affichQuest.html.twig',[
                        "questionnaire" => $questionnaire,
                        "form"          =>$form->createView()
                    ]
                   
                );
            }
      
        }
        return $this->render(
            'home/affichQuest.html.twig',[
                "questionnaire" => $questionnaire,
                "form"          =>$form->createView()
            ]
           
        );
       
    }
     /**
     * @Route("/resultat/profile", name="resultat")
     */
    public function resultat(Request $request,Question $question = null,Questionnaire $questionnaire = null){
        $reponse = new Reponse();
        $questRepo = $this->getDoctrine()->getRepository(Questionnaire::class);
        $questionnaires = $questRepo->findAll();
        
        // $form = $this->createForm(ReponseType::class, $question);
        return $this->render(
            'home/affich.html.twig', [
                "questionnaires" => $questionnaires,
            ]
           
        );
    }
}
