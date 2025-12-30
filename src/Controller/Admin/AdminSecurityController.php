<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminSecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('admin_dashboard');
    }
}