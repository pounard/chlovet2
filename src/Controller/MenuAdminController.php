<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MenuAdminController extends AbstractController
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
