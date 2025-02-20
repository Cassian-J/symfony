<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    public function isTaskDue(Task $task, \DateTime $date): bool
    {
        $today = (int) $date->format('N'); 

        if ($task->getPeriodicity() === 'daily') {
            return true; // Daily habits are always due
        }

        // Weekly habit
        $days = explode(',', $task->getDays());
        return in_array($today, $days);
    }
}
