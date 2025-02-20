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
    private CookieController $cookieController;

    public function __construct(CookieController $cookieController)
    {
        $this->cookieController = $cookieController;
    }

    #[Route('/invitation/send', name: 'invitation.send')]
    public function send(Request $request, EntityManagerInterface $em): Response
    {
        $userUuid = $this->cookieController->getCookie($request);
        if(!is_string($userUuid )){
            return $this->cookieController->message('danger','utilisateur non authentifié','app_register');
        }
        $user = $this->cookieController->getUserByCookie($userUuid, $em);
        if(!$user instanceof Users){
            return $this->cookieController->message('danger','utilisateur inexistant','app_register');
        }
        $group = $this->cookieController->getGroupsByUser($user, $em);
        if(!$group instanceof Groups){
            return $this->cookieController->message('danger','groupe inexistant','groups.create');
        }
        $invitation = new Invitation();
        $form = $this->createForm(InvitationType::class, $invitation);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $pseudo = $form->get('search')->getData();
            $recever = $em->getRepository(Users::class)->findOneBy(['Pseudo' => $pseudo]);

            if (!$recever) {
                return $this->cookieController->message('danger',"Utilisateur '$pseudo' introuvable.",'invitation.send');
            }

            $invitation->setRecever($recever);
            $invitation->setSender($user);
            $invitation->setWhichGroup($group);

            $em->persist($invitation);
            $em->flush();

            return $this->cookieController->message('success',"Invitation envoyée à $pseudo !",'app_home');
        }

        return $this->render('invitation/send.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/invitation/get', name: 'invitation.get')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $userUuid = $this->cookieController->getCookie($request);
        if(!is_string($userUuid )){
            return $this->cookieController->message('danger','utilisateur non authentifié','app_register');
        }
        $user = $this->cookieController->getUserByCookie($userUuid, $em);
        if(!$user instanceof Users){
            return $this->cookieController->message('danger','utilisateur inexistant','app_register');
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
            return $this->cookieController->message('danger','Invitation introuvable','invitation.get');
        }

        $user = $invitation->getRecever();
        $user->setGroupUuid($invitation->getWhichGroup());

        $em->remove($invitation);
        $em->flush();

        return $this->cookieController->message('success','Invitation accepté','invitation.get');
    }
    

    #[Route('/invitation/reject/{id}', name: 'invitation.reject', methods: ['POST'])]
    public function rejectInvitation(int $id, EntityManagerInterface $em): Response
    {
        $invitation = $em->getRepository(Invitation::class)->find($id);
        if (!$invitation) {
            return $this->cookieController->message('danger','Invitation introuvable','invitation.get');
        }

        $em->remove($invitation);
        $em->flush();

        return $this->cookieController->message('success','Invitation refusé','invitation.get');
    }
}

