<?php

namespace App\Controller;

use App\Entity\User;
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
        /** @var User $user */
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

                $imageData = file_get_contents($profilePicture->getPathname());
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($imageData);
                
                // Log pour déboguer
                error_log("Uploading image: size=" . strlen($imageData) . " bytes, mime=" . $mimeType);

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
        /** @var User $user */
        $user = $this->getUser();
        
        if (!$user || !$user->getProfilePicture()) {
            throw $this->createNotFoundException('No profile picture found');
        }

        $imageData = $user->getProfilePicture();
        
        // Détection du type MIME
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageData);

        return new Response(
            $imageData,
            200,
            ['Content-Type' => $mimeType]
        );
    }
}
