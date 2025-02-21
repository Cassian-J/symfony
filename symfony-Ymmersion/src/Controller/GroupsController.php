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
use App\Entity\Task;
use Ramsey\Uuid\Uuid;
use App\Controller\CookieController;

final class GroupsController extends AbstractController
{
    private CookieController $cookieController;

    public function __construct(CookieController $cookieController)
    {
        $this->cookieController = $cookieController;
    }

    #[Route('/group', name: 'groups.show')]
    public function group(Request $request,EntityManagerInterface $em): Response
    {
        $userUuid = $this->cookieController->getCookie($request);
        if(!is_string($userUuid )){
            return $this->cookieController->message('danger','utilisateur non authentifié','app_register');
        }
        $user = $this->cookieController->getUserByCookie($userUuid, $em);
        if(!$user instanceof Users){
            return $this->cookieController->message('danger','utilisateur inexistant','app_register');
        }
        $group = $user->getGroupUuid();
        if(!$group instanceof Groups){
            return $this->cookieController->message('danger','groupe inexistant','groups.create');
        }
        $users = $em->getRepository(Users::class)->findby(['GroupUuid'=>$group]);
        if (!$users) {
            return $this->cookieController->message('danger','aucun utilisateur connecté à ce groupe trouvé','app_home');
        }
        $this->cookieController->updateLastConnection($request,$em);
        return $this->render('groups/group.html.twig',[
            'users'=>$users,
            'user'=>$user->getUserUuid(),
            'group'=> $group->getCreator()
        ]);
    }

    #[Route('/groups/create', name: 'groups.create', methods:['GET','POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {

        $group = new Groups();
        $group->setGroupUuid(Uuid::uuid4()->toString());;
        $group->setPoint(50);
        $userUuid = $request->cookies->get('user_uuid');

        $userUuid = $this->cookieController->getCookie($request);
        if(!is_string($userUuid )){
            return $this->cookieController->message('danger','utilisateur non authentifié','app_register');
        }
        $user = $this->cookieController->getUserByCookie($userUuid, $entityManager);
        if(!$user instanceof Users){
            return $this->cookieController->message('danger','utilisateur inexistant','app_register');
        }
        if ($user->getGroupUuid() instanceof Groups){
            return $this->cookieController->message('danger',"vous faites deja partis d'un groupe",'groups.show');
        }

        $group->setCreator($user);
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setGroupUuid($group);
            $entityManager->persist($group);
            $entityManager->flush();
            return $this->cookieController->message('success','groupe créé','app_home');
        }

        return $this->render('groups/create.html.twig', [
            'group' => $form->createView(),
        ]);
    }

    #[Route('/groups/delete', name: 'groups.delete',methods:['DELETE'])]
    public function delete(Request $request, EntityManagerInterface $em)
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

        $users = $em->getRepository(Users::class)->findby(['GroupUuid'=>$group]);
        if (!$users) {
            return $this->cookieController->message('danger','aucun utilisateur connecté à ce groupe trouvé','app_home');
        }
        foreach($users as $usertmp){
            $usertmp->setGroupUuid(null);
        }
        $em->remove($group);
        $em->flush();
        return $this->cookieController->message('success','groupe supprimé','app_home');
    }
    
    #[Route('/groups/quit', name: 'groups.quit')]
    public function quit(Request $request, EntityManagerInterface $em)
    {   
        $userUuid = $this->cookieController->getCookie($request);
        if(!is_string($userUuid )){
            return $this->cookieController->message('danger','utilisateur non authentifié','app_register');
        }
        $user = $this->cookieController->getUserByCookie($userUuid, $em);
        if(!$user instanceof Users){
            return $this->cookieController->message('danger','utilisateur inexistant','app_register');
        }
        $groupName = $user->getGroupUuid()->getGroupUuid();
        $user->setGroupUuid(null);
        $tasks = $em->getRepository(Task::class)->findby(['UserUuid'=>$user]);
        
        foreach($tasks as $task){
            $em->remove($task);
        }
        $em->flush();
        return $this->cookieController->message('success',"vous êtes partis du groupe $groupName",'app_admin');
    }
}
