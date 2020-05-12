<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Likes;
use App\Entity\Question;
use App\Entity\Proposition;
use App\Entity\Questionnaire;
use App\Form\QuestionnaireType;
use App\Repository\UserRepository;
use App\Repository\LikesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\QuestionnaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;


class QuestionnaireController extends AbstractController 
{
    /**
     * @Route("/", name="toto")
     * 
     */  
    public function first(){
        // var_dump($this);die;
        return $this->redirectToRoute('app_login');
    }
    
   
    /**
     * @Route("/admin", name="questionnaire")
     * @Route("/admin/modif/{id}", name="modif")
     */                 
    public function index(AccessDecisionManagerInterface $access,SecurityController $security,Request $request,Questionnaire $questionnaire = null,EntityManagerInterface $manager,UserRepository $userRepo)
    {
        if(!$questionnaire){
            $questionnaire = new questionnaire();
        }
        $users = $userRepo->findAll();
        $user = $this->getUser();
        if($user){
            $form = $this->createForm(QuestionnaireType::class,$questionnaire);
            $questRepo = $this->getDoctrine()->getRepository(Questionnaire::class);
            $questionnaires = $questRepo->findAll();
            if($request){
                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid()){
                    $manager->persist($questionnaire);
                    $manager->flush();
        
                    return $this->redirectToRoute('questionnaire');
                }
            }
            return $this->render('questionnaire/index.html.twig', [
                'form' => $form->createView(),
                'questionnaires' => $questionnaires,
                'users' => $users
            ]);
        }
        return $this->redirectToRoute('app_login');
    }
     /**
     * @Route("/admin/delete/{id}", name="delete")
     * 
     */ 
    public function delete(Request $request,Questionnaire $questionnaire){
        // var_dump($questionnaire->getId());die;
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($questionnaire);
        $manager->flush();
        return $this->redirectToRoute('questionnaire');
    }
    /**
     * @Route("/admin/deleteProp/{id}", name="deleteProp")
     * 
     */ 
    public function deleteProp(Request $request,Proposition $proposition){
        $question = $proposition->getQuestion();
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($proposition);
        $manager->flush();
        $this->addFlash('success', 'proposition supprimée avec succès');
        return $this->redirectToRoute('newProp',array("id" => $question->getId()));
        
    } 
    /**
     * @Route("/admin/supprQuestion/{id}", name="supprQuestion")
     * 
     */ 
    public function supprQuestion(Request $request,Question $question){
        $questionnaire = $question->getQuestionnaire();
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($question);
        $manager->flush();
        $this->addFlash('success', 'question supprimée avec succès');
        return $this->redirectToRoute('home',array("id" => $questionnaire->getId()));
        
    }  

    /**
     * @Route("/questionnaire/{id}/like", name="likes")
     */
    public function like(Questionnaire $questionnaire, EntityManagerInterface $manager, LikesRepository $likeRepo) : Response{
        $user = $this->getUser();
        if(!$user) return $this->json([
            "code" => 403,
            "message" => "non"
        ],403);
       
        if($questionnaire->isLikedByUser($user)){
            $like = $likeRepo->findOneBy([
                "questionnaire" => $questionnaire,
                "user"          => $user
            ]);
            $manager->remove($like);
            $manager->flush();
            return $this->json([
                "code" => 200,
                "message" => "like supprimé !!!",
                "likes" => $likeRepo->count(["questionnaire" => $questionnaire])

            ],200);
        }
        $like = new Likes();
        $like->setQuestionnaire($questionnaire)
            ->setUser($user);
        $manager->persist($like);
        $manager->flush();
        return $this->json([
            "code" => 200,
            "message" => "like Ajouté !!!",
            "likes" => $likeRepo->count(["questionnaire" => $questionnaire])
        ],200);
    }
}
