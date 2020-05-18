<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Proposition;
use App\Form\PropositionType;
use App\Form\AjaxPropositionType;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PropositionRepository;
use App\Repository\QuestionnaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AjaxController extends AbstractController
{
    // /**
    //  * @Route("/ajax", name="ajax")
    //  */
    // public function index()
    // {
    //     return $this->render('ajax/index.html.twig', [
    //         'controller_name' => 'AjaxController',
    //     ]);
    // }

/**
* @Route("/ajax", name="ajax_action")
*/
    public function ajaxAction(Request $request, QuestionnaireRepository $questionnaire)
    {
        $result = $questionnaire->recherche($request->get('result'));
        // $tab = [];
        // foreach ($result as $questionnaire) {
        //     array_push($tab,$questionnaire->getName());
        // }
        // // var_dump( $result);die;
        // $result = json_encode($tab);
        // return  new JsonResponse($tab);
        // return $this->json(['message'=> $tab],200);
        // var_dump($result);
        return $this->render('ajax/index.html.twig', [
            'result' => $result
        ]);die;
    }
    
    /**
    * @Route("/addAjaxProp/{id}", name="addAjaxProp")
    */
    public function addAjaxProp(Request $request,QuestionRepository $questRepo = null)
    {
        $proposition = new Proposition();
        $form = $this->createForm(AjaxPropositionType::class, $proposition);
        $question = $questRepo->findOneBy([
            "id" => $request->get('id')
            ]);
            // dump($question);die;
        if($question){
            // $form->remove("question");           
            $proposition->setQuestion($question);
        }
        
        return $this->render('ajax/prop.html.twig', [
            "result" => $form->createView(),
            "quest_id" => $question->getId()
        ]);die;
       
    }
    /**
    * @Route("/AjaxProp", name="AjaxProp")
    */
    public function AjaxProp(Request $request,QuestionRepository $questRepo = null,EntityManagerInterface $manager)
    {
        $id_quest = $request->get("id");
        $text = $request->get("text");
        if($text != ""){

            $correct = $request->get("correct");
            // dump($correct);die;
            $proposition = new Proposition();
            $form = $this->createForm(AjaxPropositionType::class, $proposition);
            $question = $questRepo->findOneBy([
                "id" => $request->get('id')
                ]);
            $proposition->setCorrect($correct);
            $proposition->setText($text);
            $proposition->setQuestion($question);
            
            $manager->persist($proposition);
            $manager->flush();
            $id = $proposition->getId();
            $text = $proposition->getText();
            $array = [
                "id" => $id,
                "text" => $text
            ];
            return $this->render('ajax/prop.html.twig', [
                "proposition" => $proposition
            ]);die;
        }
       
    }
    /**
    * @Route("/DelAjaxProp", name="DelAjaxProp")
    */
    public function DelAjaxProp(Request $request,PropositionRepository $propRepo ,EntityManagerInterface $manager)
    {
        
        $id_prop = $request->get("id");
      $proposition = $propRepo->findOneBy([
          "id" => $id_prop
      ]);
        // dump( $request->get("id"));die;
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($proposition);
        $manager->flush();
       
            return $this->json([
                "code" => 200,
                "id"   => $id_prop,
                "quest_id" => $proposition->getQuestion()->getId(),
                "message" => "propsosition supprimée"
            ],200);die;
       
       
    }
}


