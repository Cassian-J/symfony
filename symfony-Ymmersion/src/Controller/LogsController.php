<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\GroupLogs;
use App\Entity\Task;
use App\Entity\Users;
use App\Entity\Groups;
final class LogsController extends AbstractController
{
    private CookieController $cookieController;

    public function __construct(CookieController $cookieController)
    {
        $this->cookieController = $cookieController;
    }
    #[Route('/logs', name: 'logs.show')]
    public function index(Request $request,EntityManagerInterface $em): Response
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
        $logs = $em->getRepository(GroupLogs::class)->findby(['GroupUuid'=>$group]);
        if (empty($logs)){
            return $this->render('logs/logs.html.twig', [
                'logs' => null,
                'user'=> $user
            ]);
        } else {
            $taskIds = [];
            foreach($logs as $log){
                $taskIds[] = $log->getTaskId();
            }
            $tasks = $em->getRepository(Task::class)->findBy(['id' => $taskIds]);

            $taskNames = [];
            foreach ($tasks as $task) {
                $taskNames[$task->getId()] = $task->getTitle();  
            }

            return $this->render('logs/logs.html.twig', [
                'logs' => $logs,
                'taskNames' => $taskNames,
                'user' => $user
            ]);
        }
        
    }
}
