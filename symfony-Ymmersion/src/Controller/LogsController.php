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
public function index(Request $request, EntityManagerInterface $em): Response
{
    $globalLogs=[];
    $userUuid = $this->cookieController->getCookie($request);
    if (!is_string($userUuid)) {
        return $this->cookieController->message('danger', 'utilisateur non authentifié', 'app_register');
    }

    $user = $this->cookieController->getUserByCookie($userUuid, $em);
    if (!$user instanceof Users) {
        return $this->cookieController->message('danger', 'utilisateur inexistant', 'app_register');
    }

    $logs = $em->getRepository(GroupLogs::class)->findBy(['GroupUuid' => $user->getGroupUuid()]);

    if (empty($logs)) {
        return $this->render('logs/logs.html.twig', [
            'logs' => null,
            'user' => $user
        ]);
    }
    foreach($logs as $log){
        $globalLogs[] = [
            'date' => $log->getDate(),
            'point' => $log->getPoint(),
            'taskTitle' => $log->getTaskId() ? $log->getTaskId()->getTitle() : 'Tâche inconnue',
            'userPseudo' => $log->getUserUuid() ? $log->getUserUuid()->getPseudo() : 'Utilisateur inconnu',
        ];
    }

    return $this->render('logs/logs.html.twig', [
        'logs' => $globalLogs,
        'user' => $user
    ]);
}
}
