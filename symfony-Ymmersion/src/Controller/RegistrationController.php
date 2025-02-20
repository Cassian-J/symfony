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
            
            $user->setEmail($form->get('email')->getData());
            $user->setPseudo($form->get('pseudo')->getData());
            
            $existingUser = $entityManager->getRepository(Users::class)->findOneBy(['Email' => $user->getEmail()]);
            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé par un autre utilisateur.');
                return $this->redirectToRoute('app_register');
            }

            $existingUser = $entityManager->getRepository(Users::class)->findOneBy(['Pseudo' => $user->getPseudo()]);
            if ($existingUser) {
                $this->addFlash('error', 'Ce pseudo est déjà utilisé par un autre utilisateur.');
                return $this->redirectToRoute('app_register');
            }

            $user->setPwd($userPasswordHasher->hashPassword($user, $plainPassword));

            $user->setUserUuid(Uuid::uuid4()->toString());
            $user->setLastConnection(new \DateTime());

            $entityManager->persist($user);
            $entityManager->flush();
            $cookie = Cookie::create('user_uuid')
                ->withValue($user->getUserUuid())
                ->withExpires(strtotime('+30 days'))
                ->withSecure(false)
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