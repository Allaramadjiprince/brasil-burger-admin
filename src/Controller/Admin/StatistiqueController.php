<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StatistiqueController extends AbstractController
{
    #[Route('/admin/statistique', name: 'app_admin_statistique_index')]
    public function index(): Response
    {
        return $this->render('admin/statistique/index.html.twig', [
            'controller_name' => 'StatistiqueController',
        ]);
    }
}