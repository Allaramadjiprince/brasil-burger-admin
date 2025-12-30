<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RapportController extends AbstractController
{
    #[Route('/admin/rapports/journalier', name: 'app_admin_rapport_journalier')]
    public function journalier(): Response
    {
        return $this->render('admin/rapport/journalier.html.twig', [
            'controller_name' => 'RapportController',
        ]);
    }
}