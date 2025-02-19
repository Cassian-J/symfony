<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\ProfilePictureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        /** @var Users $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfilePictureType::class, $user);
        $form->handleRequest($request);

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
                    $this->addFlash('error', $violations[0]->getMessage());
                    return $this->redirectToRoute('app_admin');
                }

                // Convertir le fichier en données binaires pour stockage BLOB
                $imageData = file_get_contents($profilePicture->getPathname());
                
                $user->setProfilePicture($imageData);
                $entityManager->persist($user);
                $entityManager->flush();
                
                $this->addFlash('success', 'Photo de profil mise à jour avec succès');
                return $this->redirectToRoute('app_admin');
            }
        }

        return $this->render('admin/index.html.twig', [
            'form' => $form,
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
