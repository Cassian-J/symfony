<?php

namespace App\Controller;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use App\Entity\Groups;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use function Symfony\Component\Clock\now;
final class CookieController extends AbstractController
{
    public function updateLastConnection(Request $request,EntityManagerInterface $em)
    {
        $now = new DateTimeImmutable();
        $userUuid=$this->getCookie($request);
        $user = $this->getUserByCookie($userUuid,$em);
        $user->setLastConnection($now);
    }
    public function getCookie(Request $request)
    {
        $userUuid = $request->cookies->get('user_uuid');
        return $userUuid;
    }
    public function getUserByCookie(string $userUuid,EntityManagerInterface $em)
    {
        $user = $em->getRepository(Users::class)->find($userUuid);
        return $user;
    }
    public function getGroupsByUser(Users $user,EntityManagerInterface $em)
    {
        $groups = $em->getRepository(Groups::class)->findby(['Creator'=>$user]);
        if ($groups===[]){
            return null;
        }
        return $groups[0];
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
    public function message(string $type,string $message,string $route)
    {
        $this->addFlash($type, $message);
        return $this->redirectToRoute($route);
    }
}
