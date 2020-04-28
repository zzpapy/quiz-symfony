<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(){
        
        $repo = $this->getDoctrine()->getRepository(Question::class);
        
        $questions = $repo->findBy([], ['title' => 'ASC']);

        return $this->render(
            'home/index.html.twig', [
                "questions" => $questions
            ]
           
        );
    }

    /**
     * @Route("/{id<\d+>}", name="question")
     */
    public function question(Question $question){
        
        return $this->render(
            'home/question.html.twig', [
                "question" => $question
            ]
        );
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
            return $this->redirectToRoute('home');
        }

        return $this->render(
            "home/new.html.twig", [
                "form" => $form->createView()
            ]
        );
    }
}
