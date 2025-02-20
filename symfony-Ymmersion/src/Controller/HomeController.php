<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\GroupLogs;
use Ramsey\Uuid\Uuid;
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
        $user = $this->cookieController->getUserByCookie($userUuid, $entityManager);
        $group = $this->cookieController->getGroupsByUser($user, $entityManager);

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
        $user = $this->cookieController->getUserByCookie($userUuid, $entityManager);
        $group = $this->cookieController->getGroupsByUser($user, $entityManager);

        if ($task->getUserUuid()!=$user){
            $this->addFlash(
               'danger',
               'This is not your task'
            );
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            $this->addFlash(
               'success',
               'Task successfully updated'
            );
            return $this->redirectToRoute('app_home');
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
        $user = $this->cookieController->getUserByCookie($userUuid, $entityManager);
        $group = $this->cookieController->getGroupsByUser($user, $entityManager);

        $task = new Task();
        
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $task->setUserUuid($user);
            $task->setGroupUuid($group);
            $task->setCreatedAt(new \DateTimeImmutable());

            //$days = $form->get('Days')->getData();
            // if ($days) {
            //     $task->setDays(implode(',', $days));
            // } else {
            //     $habit->setDays(null); // Or an empty string '' if you prefer
            // }

            $entityManager->persist($task);
            $entityManager->flush();
            $this->addFlash(
               'success',
               'Task successfully created'
            );
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/createTask.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete-task', name: 'task.delete', methods: ['DELETE'])]
    public function deleteTask(Request $request,Task $task, EntityManagerInterface $entityManager)
    {
        $userUuid = $this->cookieController->getCookie($request);
        $user = $this->cookieController->getUserByCookie($userUuid, $entityManager);
        $group = $this->cookieController->getGroupsByUser($user, $entityManager);

        if ($task->getUserUuid()!=$user){
            $this->addFlash(
               'danger',
               'This is not your task'
            );
            return $this->redirectToRoute('app_home');
        }

        $entityManager->remove($task);
        $entityManager->flush();
        $this->addFlash(
           'success',
           'Task successfully deleted'
        );
        return $this->redirectToRoute('app_home');
    }

    #[Route('/{id}/validate-task', name: 'task.validate')]
    public function validateTask(Request $request,Task $task, EntityManagerInterface $entityManager)
    {

        $userUuid = $this->cookieController->getCookie($request);
        $user = $this->cookieController->getUserByCookie($userUuid, $entityManager);
        $group = $this->cookieController->getGroupsByUser($user, $entityManager);

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
        $user = $this->cookieController->getUserByCookie($userUuid, $entityManager);
        $group = $this->cookieController->getGroupsByUser($user, $entityManager);

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
