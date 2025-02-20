<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use App\Entity\Groups;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
final class CookieController extends AbstractController
{
    
    public function getCookie(Request $request)
    {
        $userUuid = $request->cookies->get('user_uuid');
        if (!$userUuid) {
            $this->addFlash('error', 'Utilisateur non authentifié');
            return $this->redirectToRoute('app_register');
        }
        return $userUuid;
    }
    public function getUserByCookie(string $userUuid,EntityManagerInterface $em)
    {
        $user = $em->getRepository(Users::class)->find($userUuid);
        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable');
            return $this->redirectToRoute('app_register');
        }
        return $user;
    }
    public function getGroupsByUser(Users $user,EntityManagerInterface $em)
    {
        $groups = $em->getRepository(Groups::class)->findby(['Creator'=>$user]);
        if (!$groups) {
            $this->addFlash('error', 'groupe introuvable');
            return $this->redirectToRoute('groups.create');
        }
        return $groups;
    }
    public function setCookie(string $user)
    {
        $cookie = Cookie::create('user_uuid')
                ->withValue($user)
                ->withExpires(strtotime('+30 days'))
                ->withSecure(false)
                ->withHttpOnly(true)
                ->withPath('/');
        return $cookie;
    }
}
