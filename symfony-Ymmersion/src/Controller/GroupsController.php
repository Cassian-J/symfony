<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use App\Form\GroupType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Groups;
use Ramsey\Uuid\Uuid;

final class GroupsController extends AbstractController
{
    
    #[Route('/groups', name: 'groups.show')]
    public function index(Request $request): Response
    {
        return $this->render('groups/group.html.twig');
    }

    /*#[Route('/groups/{id}', name: 'groups.test',requirements:['id'=>'\d+'])]
    public function test(Request $request,int $id): Response
    {
        return $this->render('groups/test.html.twig',[
            'id'=>$id
        ]);
    }*/
    #[Route('/groups/create', name: 'groups.create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $group = new Groups();
        $group->setGroupUuid(Uuid::uuid4()->toString());; // Génère un UUID aléatoire
        $group->setPoint(50); // Définit les points de base
        // Récupération du UserUuid depuis le cookie
        $userUuid = $request->cookies->get('user_uuid');
        if (!$userUuid) {
            throw new \Exception('Utilisateur non authentifié');
        }

        $user = $entityManager->getRepository(Users::class)->find($userUuid);
        if (!$user) {
            throw new \Exception('Utilisateur introuvable');
        }

        $group->setCreator($user);
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setGroupUuid($group);
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('groups/create.html.twig', [
            'group' => $form->createView(),
        ]);
    }
}
