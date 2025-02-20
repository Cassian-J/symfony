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
use App\Controller\CookieController;

final class GroupsController extends AbstractController
{
    private CookieController $cookieController;

    public function __construct(CookieController $cookieController)
    {
        $this->cookieController = $cookieController;
    }

    #[Route('/groups', name: 'groups.show')]
    public function index(Request $request): Response
    {
        return $this->render('groups/group.html.twig');
    }

    #[Route('/groups/create', name: 'groups.create', methods:['GET','POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $group = new Groups();
        $group->setGroupUuid(Uuid::uuid4()->toString());;
        $group->setPoint(50);
        $userUuid = $request->cookies->get('user_uuid');

        $userUuid = $this->cookieController->getCookie($request);
        $user = $this->cookieController->getUserByCookie($userUuid, $entityManager);

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

    #[Route('/groups/delete', name: 'groups.delete',methods:['DELETE'])]
    public function delete(Request $request, EntityManagerInterface $em)
    {   
        $userUuid = $this->cookieController->getCookie($request);
        $user = $this->cookieController->getUserByCookie($userUuid, $em);
        $groups = $this->cookieController->getGroupsByUser($user, $em);

        $users = $em->getRepository(Users::class)->findby(['GroupUuid'=>$groups[0]]);
        if (!$users) {
            $this->addFlash('error', 'aucun utilisateur connecté à ce groupe trouvé');
            return $this->redirectToRoute('app_home');
        }
        foreach($users as $usertmp){
            $usertmp->setGroupUuid(null);
        }
        $em->remove($groups[0]);
        $em->flush();
        return $this->redirectToRoute('app_home');
    }
}
