<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Task;
use App\Entity\GroupLogs;
use App\Entity\Users;
use App\Entity\Groups;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Date;

final class TaskController extends AbstractController
{
    private GroupsController $groupsController;

    public function __construct(GroupsController $groupsController)
    {
        $this->groupsController = $groupsController;
    }

    // Check if a task is due based on periodicity
    public function isTaskDue(Task $task, \DateTime $date): bool
    {
        $today = $date->format('l'); 

        if ($task->getPeriodicity() === 'daily') {
            return true; // Daily habits are always due
        }
        if ($task->getPeriodicity() === 'once') {
            return !$task->isDone(); // If task with no periodicity is not done it is still due
        }

        // Weekly habit
        $days = explode(',', $task->getDays());
        return in_array($today, $days);
    }

    //Change state of previously done tasks if they are due again today
    public function findAllTasksCurrentlyDue(Users $user, \DateTime $date, EntityManagerInterface $entityManager)
    {
        $tasks = $entityManager->getRepository(Task::class)->findBy(['UserUuid'=>$user]);

        foreach ($tasks as $task) {
            if ($this->isTaskDue($task, $date)) {
                $task->setDone(false);
            } else {
                $task->setDone(true);
            }
            $entityManager->persist($task);
        }

        $entityManager->flush();
    }

    public function getAllTasksMissedSinceDate(\DateTime $startDate, \DateTime $endDate, Users $user, Groups $group, EntityManagerInterface $entityManager)
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select('t')
            ->from(Task::class, 't')
            ->join('t.GroupUuid', 'g')
            ->where('t.Periodicity != :periodicity')
            ->andWhere('g = :group')
            ->setParameter('periodicity', 'once')
            ->setParameter('group', $group);

        $tasks = $queryBuilder->getQuery()->getResult();
        $currentDate = clone $startDate;

        // For tasks due on the last day of connection, check for tasks that have been done that day and ignore them
        foreach ($tasks as $task) {
            if($this->isTaskDue($task, $currentDate) && $task->isGroupTask() && !$task->isDone()) {
                $this->invalidateGroupTask($group, clone $currentDate, $task, $entityManager);
            } elseif ($this->isTaskDue($task, $currentDate) && !$task->isDone()) {
                $this->logTaskFailure(clone $currentDate, $task, $user, $group, $entityManager);
            }
        }
        $currentDate->modify('+1 day');

        //Check the rest of the days without checking if they are marked as done
        while ($currentDate < $endDate) {
            foreach ($tasks as $task) {
                if($this->isTaskDue($task, $currentDate) && $task->IsGroupTask()) {
                    $this->invalidateGroupTask($group, clone $currentDate, $task, $entityManager);
                } elseif ($this->isTaskDue($task, $currentDate)) {
                    $this->logTaskFailure(clone $currentDate, $task, $user, $group, $entityManager);
                }
            }
            $currentDate->modify('+1 day');
        }
        $entityManager->flush();
    }

    public function logTaskFailure(\DateTime $date, Task $task, Users $user, Groups $group, EntityManagerInterface $entityManager)
    {
        $grouplog = new GroupLogs();
        switch($task->getDifficulty()){
            case 1:
                $grouplog->setPoint(-8);
                break;
            case 2:
                $grouplog->setPoint(-5);
                break;
            case 3:
                $grouplog->setPoint(-2);
                break;
            case 4:
                $grouplog->setPoint(-1);
                break;
        }
        $grouplog->setTaskId($task);
        $grouplog->setUserUuid($user);
        $grouplog->setGroupUuid($group);
        $grouplog->setDate($date);

        $entityManager->persist($grouplog);

        $group->setPoint($group->getPoint()+$grouplog->getPoint());
        $entityManager->persist($group);
    }

    public function getAllPointsObtainedSinceLastConnection(\DateTime $date, Groups $group, EntityManagerInterface $entityManager): array
    {
        $total = [
            "totalLost" => 0,
            "totalGained" => 0,
            "totalAll" => 0
        ];
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select('gl')
            ->from(GroupLogs::class, 'gl')
            ->where('gl.GroupUuid = :group')
            ->andWhere('gl.date >= :startDate')
            ->setParameter('group', $group)
            ->setParameter('startDate', $date)
            ->orderBy('gl.date', 'ASC');

        $groupLogs = $queryBuilder->getQuery()->getResult();
        foreach ($groupLogs as $log) {
            if ($log->getPoint() < 0) {
                $total["totalLost"] -= $log->getPoint();
            } else {
                $total["totalGained"] += $log->getPoint();
            }
            $total["totalAll"] += $log->getPoint();
        }

        return $total;
    }

    public function findAllGroupTasksCurrentlyDue(Groups $group, Users $user, \DateTime $date, EntityManagerInterface $entityManager):array
    {
        $dueTasks = [];
        $groupTasks = $entityManager->getRepository(Task::class)->findBy(['GroupUuid'=>$group, 'IsGroupTask'=>true]);
        foreach ($groupTasks as $groupTask) {
            if ($this->isTaskDue($groupTask, $date)) {
                //check in grouplogs if this user has done this grouptask at the date specified
                $queryBuilder = $entityManager->createQueryBuilder();
                $queryBuilder
                    ->select('gl')
                    ->from(GroupLogs::class, 'gl')
                    ->where('gl.UserUuid = :user')
                    ->andWhere('DATE(gl.date) = DATE(:date)') //to compare dates but not time
                    ->andWhere('gl.TaskId = :taskId')
                    ->setParameter('user', $user)
                    ->setParameter('date', $date)
                    ->setParameter('taskId', $groupTask);
                
                $groupLog = $queryBuilder->getQuery()->getResult();
                
                if ($groupLog===[]){
                    $dueTasks[] = $groupTask;
                }
            }
        }
        return $dueTasks;
    }

    public function validateGroupTask(Users $user, Groups $group, \DateTime $date, Task $task, EntityManagerInterface $entityManager)
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder
            ->select('gl')
            ->from(GroupLogs::class, 'gl')
            ->where('gl.GroupUuid = :group')
            ->andWhere('DATE(gl.date) = DATE(:date)') //to compare dates but not time
            ->andWhere('gl.TaskId = :taskId')
            ->setParameter('group', $group)
            ->setParameter('date', $date)
            ->setParameter('taskId', $task);
        
        $groupLogs = $queryBuilder->getQuery()->getResult();
        
        $nUsersInGroup = count($entityManager->getRepository(Users::class)->findBy(['GroupUuid'=>$group]));
        
        if(count($groupLogs) == $nUsersInGroup) {
            $group->setPoint($group->getPoint()+$groupLogs[0]->getPoint());
            $entityManager->persist($group);
    
            $task->setDone(true);
            $entityManager->persist($task);
        }
        
        $entityManager->flush();
    }

    public function invalidateGroupTask(Groups $group, \DateTime $date, Task $task, EntityManagerInterface $entityManager)
    {
        $usersInGroup = $entityManager->getRepository(Users::class)->findBy(['GroupUuid'=>$group]);
        foreach ($usersInGroup as $user) {
            $queryBuilder = $entityManager->createQueryBuilder();
            $queryBuilder
                ->select('gl')
                ->from(GroupLogs::class, 'gl')
                ->where('gl.UserUuid = :user')
                ->andWhere('gl.GroupUuid = :group')
                ->andWhere('DATE(gl.date) = DATE(:date)') //to compare dates but not time
                ->andWhere('gl.TaskId = :taskId')
                ->setParameter('user', $user)
                ->setParameter('group', $group)
                ->setParameter('date', $date)
                ->setParameter('taskId', $task);
            
            $groupLog = $queryBuilder->getQuery()->getResult();
            
            if ($groupLog===[]){
                $this->logTaskFailure($date, $task, $user, $group, $entityManager);
            }
        }
    }
}
