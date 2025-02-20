<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\GroupLogs;
use App\Entity\Users;
use App\Entity\Groups;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class HomeController extends AbstractController
{
    private CookieController $cookieController;

    public function __construct(CookieController $cookieController)
    {
        $this->cookieController = $cookieController;
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userUuid = $this->cookieController->getCookie($request);
        if(!is_string($userUuid )){
            return $this->cookieController->message('danger','utilisateur non authentifié','app_register');
        }
        $user = $this->cookieController->getUserByCookie($userUuid, $entityManager);
        if(!$user instanceof Users){
            return $this->cookieController->message('danger','utilisateur inexistant','app_register');
        }
        $group = $this->cookieController->getGroupsByUser($user, $entityManager);
        if(!$group instanceof Groups){
            return $this->cookieController->message('danger','groupe inexistant','groups.create');
        }
        $tasks = $entityManager->getRepository(Task::class)->findAll();

        return $this->render('home/index.html.twig', [
            'name' => $user->getPseudo(),
            'tasks' => $tasks,
        ]);
    }

    #[Route('/{id}/edit-task', name: 'task.edit', methods: ['GET', 'POST'])]
    public function editTask(Request $request,Task $task, EntityManagerInterface $entityManager)
    {
        $userUuid = $this->cookieController->getCookie($request);
        if(!is_string($userUuid )){
            return $this->cookieController->message('danger','utilisateur non authentifié','app_register');
        }
        $user = $this->cookieController->getUserByCookie($userUuid, $entityManager);
        if(!$user instanceof Users){
            return $this->cookieController->message('danger','utilisateur inexistant','app_register');
        }
        $group = $this->cookieController->getGroupsByUser($user, $entityManager);
        if(!$group instanceof Groups){
            return $this->cookieController->message('danger','groupe inexistant','groups.create');
        }
        if ($task->getUserUuid()!=$user){
            return $this->cookieController->message('danger','This is not your task','app_home');
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $days = $form->get('Days')->getData();
            if ($days) {
                $task->setDays($days);
            } else {
                $task->setDays(null);
            }
            $entityManager->flush();
            return $this->cookieController->message('success','Task successfully updated','app_home');
        }
        
        return $this->render('home/editTask.html.twig', [
            'task' => $task,
            'form' => $form
        ]);
    }

    #[Route('/create-task', name: 'task.create')]
    public function createTask(Request $request, EntityManagerInterface $entityManager)
    {
        $userUuid = $this->cookieController->getCookie($request);
        if(!is_string($userUuid )){
            return $this->cookieController->message('danger','utilisateur non authentifié','app_register');
        }
        $user = $this->cookieController->getUserByCookie($userUuid, $entityManager);
        if(!$user instanceof Users){
            return $this->cookieController->message('danger','utilisateur inexistant','app_register');
        }
        $group = $this->cookieController->getGroupsByUser($user, $entityManager);
        if(!$group instanceof Groups){
            return $this->cookieController->message('danger','groupe inexistant','groups.create');
        }
        $task = new Task();

        

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $task->setUserUuid($user);
            $task->setGroupUuid($group);
            $task->setCreatedAt(new \DateTimeImmutable());

            $days = $form->get('Days')->getData();
            if ($days) {
                $task->setDays($days); // Convertir en string avec séparateur
            } else {
                $task->setDays(null);
            }

            $entityManager->persist($task);
            $entityManager->flush();
            return $this->cookieController->message('success','Task successfully created','app_home');
        }

        return $this->render('home/createTask.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete-task', name: 'task.delete', methods: ['DELETE'])]
    public function deleteTask(Request $request,Task $task, EntityManagerInterface $entityManager)
    {
        $userUuid = $this->cookieController->getCookie($request);
        if(!is_string($userUuid )){
            return $this->cookieController->message('danger','utilisateur non authentifié','app_register');
        }
        $user = $this->cookieController->getUserByCookie($userUuid, $entityManager);
        if(!$user instanceof Users){
            return $this->cookieController->message('danger','utilisateur inexistant','app_register');
        }
        $group = $this->cookieController->getGroupsByUser($user, $entityManager);
        if(!$group instanceof Groups){
            return $this->cookieController->message('danger','groupe inexistant','groups.create');
        }
        if ($task->getUserUuid()!=$user){
            return $this->cookieController->message('danger','This is not your task','app_home');
        }

        $entityManager->remove($task);
        $entityManager->flush();
        return $this->cookieController->message('success','Task successfully deleted','app_home');
    }

    #[Route('/{id}/validate-task', name: 'task.validate')]
    public function validateTask(Request $request,Task $task, EntityManagerInterface $entityManager)
    {

        $userUuid = $this->cookieController->getCookie($request);
        if(!is_string($userUuid )){
            return $this->cookieController->message('danger','utilisateur non authentifié','app_register');
        }
        $user = $this->cookieController->getUserByCookie($userUuid, $entityManager);
        if(!$user instanceof Users){
            return $this->cookieController->message('danger','utilisateur inexistant','app_register');
        }
        $group = $this->cookieController->getGroupsByUser($user, $entityManager);
        if(!$group instanceof Groups){
            return $this->cookieController->message('danger','groupe inexistant','groups.create');
        }
        $grouplog = new GroupLogs();
        switch($task->getDifficulty()){
            case 0:
                $grouplog->setPoint(1);
                break;
            case 1:
                $grouplog->setPoint(2);
                break;
            case 2:
                $grouplog->setPoint(5);
                break;
            case 3:
                $grouplog->setPoint(10);
                break;
            default:
                $grouplog->setPoint(0);
                break;
        }
        $grouplog->setTaskId($task);
        $grouplog->setUserUuid($user);
        $grouplog->setGroupUuid($group);

        $entityManager->persist($grouplog);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }

    #[Route('/{id}/invalidate-task', name: 'task.invalidate')]
    public function invalidateTask(Request $request,Task $task, EntityManagerInterface $entityManager)
    {

        $userUuid = $this->cookieController->getCookie($request);
        if(!is_string($userUuid )){
            return $this->cookieController->message('danger','utilisateur non authentifié','app_register');
        }
        $user = $this->cookieController->getUserByCookie($userUuid, $entityManager);
        if(!$user instanceof Users){
            return $this->cookieController->message('danger','utilisateur inexistant','app_register');
        }
        $group = $this->cookieController->getGroupsByUser($user, $entityManager);
        if(!$group instanceof Groups){
            return $this->cookieController->message('danger','groupe inexistant','groups.create');
        }
        $grouplog = new GroupLogs();
        switch($task->getDifficulty()){
            case 0:
                $grouplog->setPoint(-8);
                break;
            case 1:
                $grouplog->setPoint(-5);
                break;
            case 2:
                $grouplog->setPoint(-2);
                break;
            case 3:
                $grouplog->setPoint(-1);
                break;
            default:
                $grouplog->setPoint(0);
                break;
        }
        $grouplog->setTaskId($task);
        $grouplog->setUserUuid($user);
        $grouplog->setGroupUuid($group);

        $entityManager->persist($grouplog);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}
