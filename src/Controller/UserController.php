<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use App\Entity\User;
use App\Form\RegisterType;

class UserController extends AbstractController
{   
    public function signUp(ManagerRegistry $doctrine, Request $request, UserPasswordEncoderInterface $encoder): Response
    {   
        # We create a new user.
        $user = new User();

        # We create the form.
        $form = $this->createForm(RegisterType::class, $user);

        # We manage the form and get the data.
        $form->handleRequest($request);

        # If the the form is submitted correctly, we manage the information.
        if($form->isSubmitted() && $form->isValid()) {
            $user->setRole('ROLE_USER');
            
            # Set date.
            $user->setCreatedAt(new \Datetime('now'));
            
            # Password encoder.
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            
            $user->setPassword($encoded);
            
            # Save user.
            $em = $doctrine->getManager();

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('tasks');
        }

        return $this->render('user/sign-up.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function logIn(AuthenticationUtils $autenticationUtils): Response 
    {
        $error = $autenticationUtils->getLastAuthenticationError();
		
        # Last user that tried to Log In.
		$lastUsername = $autenticationUtils->getLastUsername();
		
		return $this->render('user/log-in.html.twig', array(
			'error' => $error,
			'last_username' => $lastUsername,
		));
    }
}
