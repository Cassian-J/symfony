<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\ProfilePictureType;
use App\Form\UpdatePseudoType;
use App\Form\UpdateEmailType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AdminController extends AbstractController
{
    private CookieController $cookieController;

    public function __construct(CookieController $cookieController)
    {
        $this->cookieController = $cookieController;
    }
    #[Route('/admin', name: 'app_admin')]
    public function index(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        /** @var Users $user */
        $user = $this->getUser();
        $this->cookieController->updateLastConnection($request,$entityManager);
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ProfilePictureType::class, $user);
        $form->handleRequest($request);

        // Formulaire pour modifier le pseudo
        $pseudoForm = $this->createForm(UpdatePseudoType::class, $user);
        $pseudoForm->handleRequest($request);

        // Formulaire pour modifier l'email
        $emailForm = $this->createForm(UpdateEmailType::class, $user);
        $emailForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePicture = $form->get('profilePicture')->getData();
            
            if ($profilePicture) {
                // Validation du fichier
                $constraints = new Assert\File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG ou PNG)',
                ]);

                $violations = $validator->validate($profilePicture, $constraints);

                if (count($violations) > 0) {
                    return $this->cookieController->message('danger',$violations[0]->getMessage(),'app_admin');
                }

                // Convertir le fichier en données binaires pour stockage BLOB
                $imageData = file_get_contents($profilePicture->getPathname());
                
                $user->setProfilePicture($imageData);
                $entityManager->persist($user);
                $entityManager->flush();
    
                return $this->cookieController->message('success','Photo de profil mise à jour avec succès','app_admin');
            }
        }

        if ($pseudoForm->isSubmitted() && $pseudoForm->isValid()) {
            $newPseudo = $pseudoForm->get('pseudo')->getData();
            $existingUser = $entityManager->getRepository(Users::class)->findOneBy(['Pseudo' => $newPseudo]);

            if ($existingUser && $existingUser->getUserUuid() !== $user->getUserUuid()) {
                return $this->cookieController->message('danger','Ce pseudo est déjà utilisé par un autre utilisateur.','app_admin');
            }

            $user->setPseudo($newPseudo);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->cookieController->message('success','Votre pseudo a été mis à jour avec succès.','app_admin');
        }

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $newEmail = $emailForm->get('email')->getData();
            $existingUser = $entityManager->getRepository(Users::class)->findOneBy(['Email' => $newEmail]);

            if ($existingUser && $existingUser->getUserUuid() !== $user->getUserUuid()) {
                return $this->cookieController->message('danger','Cet email est déjà utilisé par un autre utilisateur.','app_admin');
            }

            $user->setEmail($newEmail);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->cookieController->message('success','Votre email a été mis à jour avec succès.','app_admin');
        }

        return $this->render('admin/index.html.twig', [
            'form' => $form->createView(),
            'pseudoForm' => $pseudoForm->createView(),
            'emailForm' => $emailForm->createView(),
        ]);
    }

    #[Route('/profile-picture', name: 'app_get_profile_picture')]
    public function getProfilePicture(): Response
    {
        /** @var Users $user */
        $user = $this->getUser();
        if (!$user || !$user->getProfilePicture()) {
            // Retourner une image par défaut
            $defaultImagePath = $this->getParameter('kernel.project_dir') . '/public/images/default-profile.jpg';
            return new Response(
                file_get_contents($defaultImagePath),
                200,
                ['Content-Type' => 'image/jpeg']
            );
        }

        $imageData = stream_get_contents($user->getProfilePicture());
        $imageInfo = getimagesizefromstring($imageData);

        $mimeType = $imageInfo['mime'];

        return new Response(
            $imageData,
            200,
            ['Content-Type' => $mimeType]
        );
    }
}
