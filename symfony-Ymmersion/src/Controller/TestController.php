<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/test-cookie', name: 'test_cookie')]
    public function testCookie(Request $request): Response
    {
        $cookie = $request->cookies->get('user_uuid');
        return new Response('Cookie user_uuid value: ' . ($cookie ?: 'not found'));
    }
} 