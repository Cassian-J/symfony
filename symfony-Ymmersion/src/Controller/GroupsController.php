<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GroupsController extends AbstractController
{
    #[Route('/groups/{slugs}-{id}', name: 'groups.show', requirements: ['id'=> '\d+', 'slugs'=>'[a-zA-Z0-9-]+'])]
    public function index(Request $request): Response
    {
        dd($request);
    }
}
