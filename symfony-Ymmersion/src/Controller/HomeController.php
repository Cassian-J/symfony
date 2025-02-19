<?php

namespace App\Controller;

use App\Entity\Task;
use Ramsey\Uuid\Uuid;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tasks = $entityManager->getRepository(Task::class)->findAll();

        return $this->render('home/index.html.twig', [
            'name' => 'User',
            'tasks' => $tasks,
        ]);
    }

    #[Route('/{id}/edit-task', name: 'task.edit', methods: ['GET', 'POST'])]
    public function editTask(Request $request,Task $task, EntityManagerInterface $entityManager)
    {
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
        $task = new Task();

        #TODO SET USER UUID AND GROUP UUID AUTOMATICALY WHEN CREATING TASKS
        $task->setUserUuid("test");

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
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
        $entityManager->remove($task);
        $entityManager->flush();
        $this->addFlash(
           'success',
           'Task successfully deleted'
        );
        return $this->redirectToRoute('app_home');
    }
}
