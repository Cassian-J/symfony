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

final class TaskController extends AbstractController
{
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
    public function findAllTasksCurrentlyDue(\DateTime $date, EntityManagerInterface $entityManager)
    {
        $tasks = $entityManager->getRepository(Task::class)->findAll();

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
        $tasks = $entityManager->getRepository(Task::class)->findBy(['Periodicity' => ['$ne' => 'once'], 'GroupUuid'=>$group]); //Get all tasks with periodicity from all users from the group

        $currentDate = clone $startDate;

        // For tasks due on the last day of connection, check for tasks that have been done that day and ignore them
        foreach ($tasks as $task) {
            if ($this->isTaskDue($task, $currentDate) && !$task->isDone()) {
                $this->logTaskFailure(clone $currentDate, $task, $user, $group, $entityManager);
            }
        }
        $currentDate->modify('+1 day');

        //Check the rest of the days without checking if they are marked as done
        while ($currentDate < $endDate) {
            foreach ($tasks as $task) {
                if ($this->isTaskDue($task, $currentDate)) {
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
}
