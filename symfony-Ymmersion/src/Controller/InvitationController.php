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
            $this->addFlash('error', 'Aucun Utilisateur connecter');
            return $this->redirectToRoute('app_register');
        }

        $user = $em->getRepository(Users::class)->find($userUuid);
        if (!$user) {
            $this->addFlash('error', 'Aucun Utilisateur connecter');
            return $this->redirectToRoute('app_register');
        }

        $groups = $em->getRepository(Groups::class)->findBy(['Creator' => $user]);
        if (!$groups) {
            $this->addFlash('error', "vous ne faites partis d'aucun groupes");
            return $this->redirectToRoute('groups.create');
        }
        
        $invitation->setWhichGroup($groups[0]);
        $invitation->setSender($user);

        $form = $this->createForm(InvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recever = $invitation->getRecever();
            if (!$recever) {
                $this->addFlash('error', "utilisateur inconnu");
                return $this->redirectToRoute('invitation.send');
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
            $this->addFlash('error', "utilisateur non connecter");
            return $this->redirectToRoute('app_register');
        }

        $user = $em->getRepository(Users::class)->find($userUuid);
        if (!$user) {
            $this->addFlash('error', "utilisateur non connecter");
            return $this->redirectToRoute('app_register');
        }

        $invitations = $em->getRepository(Invitation::class)->findBy(['Recever' => $user]);
        if (empty($invitations)) {
            return $this->render('invitation/get.html.twig', [
                'invitation' => null
            ]);
        }
        

        return $this->render('invitation/get.html.twig',[
            'invitation' => $invitations
        ]);
    }
    #[Route('/invitation/accept/{id}', name: 'invitation.accept', methods: ['POST'])]
    public function acceptInvitation(int $id, EntityManagerInterface $em): Response
    {
        $invitation = $em->getRepository(Invitation::class)->find($id);
        if (!$invitation) {
            $this->addFlash('error', 'Invitation introuvable');
            return $this->redirectToRoute('invitation.get');
        }

        $user = $invitation->getRecever();
        $user->setGroupUuid($invitation->getWhichGroup());

        $em->remove($invitation);
        $em->flush();

        return $this->redirectToRoute('invitation.get');
    }

    #[Route('/invitation/reject/{id}', name: 'invitation.reject', methods: ['POST'])]
    public function rejectInvitation(int $id, EntityManagerInterface $em): Response
    {
        $invitation = $em->getRepository(Invitation::class)->find($id);
        if (!$invitation) {
            $this->addFlash('error', 'Invitation introuvable');
            return $this->redirectToRoute('invitation.get');
        }

        $em->remove($invitation);
        $em->flush();

        return $this->redirectToRoute('invitation.get');
    }
}
