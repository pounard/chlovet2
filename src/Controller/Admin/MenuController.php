<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MenuController extends AbstractController
{
    use ControllerTrait;

    public function list(Request $request): Response
    {
        return $this->render('gestion/route/list.html.twig');
    }

    public function edit(Request $request): Response
    {
        return $this->render('gestion/route/edit.html.twig');
    }
}
