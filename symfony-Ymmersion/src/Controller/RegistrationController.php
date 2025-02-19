<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Cookie;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_admin');
        }

        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            
            // Set email and pseudo from the form
            $user->setEmail($form->get('email')->getData());
            $user->setPseudo($form->get('pseudo')->getData());
            
            // encode the plain password
            $user->setPwd($userPasswordHasher->hashPassword($user, $plainPassword));

            // Set UUID manually
            $user->setUserUuid(Uuid::uuid4()->toString());
            // Set last connection
            $user->setLastConnection(new \DateTime());

            $entityManager->persist($user);
            $entityManager->flush();
            $cookie = Cookie::create('user_uuid')
                ->withValue($user->getUserUuid())
                ->withExpires(strtotime('+30 days')) // Expire après 30 jours
                ->withSecure(false) // Mettre à true en production (HTTPS)
                ->withHttpOnly(true)
                ->withPath('/');
            $response = $security->login($user, AppAuthenticator::class, 'main');
            $response->headers->setCookie($cookie);
            return $response;
        }



        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}