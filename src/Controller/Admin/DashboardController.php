<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(): Response
    {
        $stats = [
            'commandes_jour' => 0,
            'recette_jour' => 0,
            'produits_populaires' => [],
            'commandes_en_cours' => 0,
            'commandes_annulees' => 0,
            'livreurs_disponibles' => 0,
        ];

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
        ]);
    }

    public function commande(): Response
    {
        return $this->render('admin/commande/index.html.twig');
    }

    public function client(): Response
    {
        return $this->render('admin/client/index.html.twig');
    }

    public function produit(): Response
    {
        return $this->render('admin/produit/index.html.twig');
    }

    public function livreur(): Response
    {
        return $this->render('admin/livreur/index.html.twig');
    }

    public function paiement(): Response
    {
        return $this->render('admin/paiement/index.html.twig');
    }

    public function zone(): Response
    {
        return $this->render('admin/zone_livraison/index.html.twig');
    }
}