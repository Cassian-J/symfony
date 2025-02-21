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
    private TaskController $taskController;

    public function __construct(CookieController $cookieController,TaskController $taskController)
    {
        $this->cookieController = $cookieController;
        $this->taskController = $taskController;
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
        $group = $user->getGroupUuid();
        
        $total = null;
        $tasks=null;
        //$newConnectionDate = new \DateTime(); //Today
        $newConnectionDate = new \DateTime('2025-02-28 10:30:00'); //Set custom date

        // Check if this is the first connection of the day
        
        $lastConnection = $user->getLastConnection();
        if ($group!==null){
        if ($newConnectionDate->format('Y-m-d') !== $lastConnection->format('Y-m-d')) {
            
            $today = $newConnectionDate;
            $queryBuilder = $entityManager->createQueryBuilder();
            $queryBuilder
                ->select('u')
                ->from(Users::class, 'u')
                ->where('u.GroupUuid = :group')
                ->andWhere('u.lastConnection >= :today')
                ->setParameter('group', $group)
                ->setParameter('today', $today)
                ->orderBy('u.lastConnection', 'ASC')
                ->setMaxResults(1);

            $usersConnectedToday = $queryBuilder->getQuery()->getResult();
            
            // Get the first user connected today and check if it is the current user

            if (empty($usersConnectedToday) || $usersConnectedToday[0]->getUserUuid() === $user->getUserUuid()) {
                $oldestLastConnectedUser = $entityManager->getRepository(Users::class)->findBy(['GroupUuid' => $group], ['lastConnection' => 'ASC'], 1 );
                if (!empty($oldestLastConnectedUser)) { //Error Case
                    $oldestUserConnection = $oldestLastConnectedUser[0]->getLastConnection();
                    // Calculate and update grouplog with all tasks not done between today and the last time a user of the group was connected
                    $this->taskController->getAllTasksMissedSinceDate($oldestUserConnection, (clone $newConnectionDate)->modify('-1 day'), $user, $group, $entityManager);
                }
            }

            // Get all the points obtained and lost from all users since current user's last connection
            $total = $this->taskController->getAllPointsObtainedSinceLastConnection($lastConnection, $group, $entityManager);

            $this->taskController->findAllTasksCurrentlyDue($user, $newConnectionDate, $entityManager); //Rested the done marker for todays' tasks
        } 

        $tasks = $entityManager->getRepository(Task::class)->findBy(['UserUuid'=>$user, 'Done'=>false]);
        $this->cookieController->updateLastConnection($request,$entityManager);
            
        }
        return $this->render('home/index.html.twig', [
            'name' => $user->getPseudo(),
            'total' => $total,
            'tasks' => $tasks,
            'user'=> $user,
            'group'=>$group
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
        $group = $user->getGroupUuid();
        if(!$group instanceof Groups){
            return $this->cookieController->message('danger','groupe inexistant','groups.create');
        }
        if ($task->getUserUuid()!=$user){
            return $this->cookieController->message('danger','This is not your task','app_home');
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->cookieController->message('success','Task successfully updated','app_home');
        }
        
        return $this->render('home/editTask.html.twig', [
            'task' => $task,
            'form' => $form,
            'user'=> $user
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
        $group = $user->getGroupUuid();
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
            $task->setDone(false);

            $entityManager->persist($task);
            $entityManager->flush();
            return $this->cookieController->message('success','Task successfully created','app_home');
        }

        return $this->render('home/createTask.html.twig', [
            'form' => $form,
            'user'=> $user
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
        $group =$user->getGroupUuid();
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
        $group = $user->getGroupUuid();
        if(!$group instanceof Groups){
            return $this->cookieController->message('danger','groupe inexistant','groups.create');
        }


        $grouplog = new GroupLogs();

        switch($task->getDifficulty()){
            case 1:
                $grouplog->setPoint(1);
                break;
            case 2:
                $grouplog->setPoint(2);
                break;
            case 3:
                $grouplog->setPoint(5);
                break;
            case 4:
                $grouplog->setPoint(10);
                break;
        }
        $grouplog->setTaskId($task);
        $grouplog->setUserUuid($user);
        $grouplog->setGroupUuid($group);
        $grouplog->setDate(new \DateTime());
        $entityManager->persist($grouplog);

        $group->setPoint($group->getPoint()+$grouplog->getPoint());
        $entityManager->persist($group);

        $task->setDone(true);
        $entityManager->persist($task);

        $entityManager->flush();

        $points = $grouplog->getPoint();
        $point_str = (1 == $points) ? 'point' : 'points';
        return $this->cookieController->message('success',"Congrats, you won $points $point_str",'app_home');
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
        $group =$user->getGroupUuid();
        if(!$group instanceof Groups){
            return $this->cookieController->message('danger','groupe inexistant','groups.create');
        }

        $this->taskController->logTaskFailure(new \DateTime(), $task, $user, $group, $entityManager);
        
        $task->setDone(true);
        $entityManager->persist($task);

        $entityManager->flush();

        $points = abs($grouplog->getPoint());
        $point_str = (1 == $points) ? 'point' : 'points';
        return $this->cookieController->message('danger',"Dang, you lost $points $point_str.",'app_home');
    }
}
