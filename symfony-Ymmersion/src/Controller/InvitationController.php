<?php

namespace App\Controller;

use App\Form\InvitationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Invitation;
use App\Entity\Users;
use App\Entity\Groups;
use Doctrine\ORM\EntityManagerInterface;

final class InvitationController extends AbstractController
{
    #[Route('/invitation/send', name: 'invitation.send')]
    public function send(Request $request, EntityManagerInterface $em): Response
    {
        $invitation = new Invitation();
        
        $userUuid = $request->cookies->get('user_uuid');
        if (!$userUuid) {
            throw new \Exception('Utilisateur non authentifié');
        }

        $user = $em->getRepository(Users::class)->find($userUuid);
        if (!$user) {
            throw new \Exception('Utilisateur introuvable');
        }

        $groups = $em->getRepository(Groups::class)->findBy(['Creator' => $user]);
        if (!$groups) {
            throw new \Exception('Aucun groupe trouvé pour cet utilisateur');
        }
        
        $invitation->setWhichGroup($groups[0]);
        $invitation->setSender($user);

        $form = $this->createForm(InvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recever = $invitation->getRecever();
            if (!$recever) {
                throw new \Exception('Utilisateur destinataire introuvable');
            }

            $em->persist($invitation);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('invitation/send.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/invitation/get', name: 'invitation.get')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $userUuid = $request->cookies->get('user_uuid');
        if (!$userUuid) {
            throw new \Exception('Utilisateur non authentifié');
        }

        $user = $em->getRepository(Users::class)->find($userUuid);
        if (!$user) {
            throw new \Exception('Utilisateur introuvable');
        }

        $invitations = $em->getRepository(Invitation::class)->findBy(['Recever' => $user]);
        if (!$invitations) {
            throw new \Exception('Aucun groupe trouvé pour cet utilisateur');
        }
        

        return $this->render('invitation/get.html.twig',[
            'invitation' => $invitations
        ]);
    }
}
