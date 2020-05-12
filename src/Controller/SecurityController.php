<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/security/{id<\d+>}", name="security")
     */
    public function index(User $user,Request $request){
        $form = $this->createForm(UserType::class, $user);
        $questRepo = $this->getDoctrine()->getRepository(User::class);
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->findOneBy(["id" => $user->getId()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
           

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }
        
        // $form->add('roles', ChoiceType::class,[
        //     "choices" =>"$user->getRoles()"]);
        // $form->add('likes', ChoiceType::class, [
        //     "choices" =>$user->getLikes()]);
        
        return $this->render(
            'security/affichUser.html.twig', [
                "user" => $user,
                "form" => $form->createView()
            ]
           
        );
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $lastUsername = $authenticationUtils->getLastUsername();
            return $this->redirectToRoute('questionnaire');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {   
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    public function view(AccessDecisionManagerInterface $accessDecisionManager, User $user,Response $response = null){
        $token = new UsernamePasswordToken($user, 'none', 'none', $user->getRoles());
        if ($accessDecisionManager->decide($token,['ROLE_ADMIN'])) {
             // L'utilisateur $user a le rôle ROLE_ADMIN
        }
        return $response;
    }
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
