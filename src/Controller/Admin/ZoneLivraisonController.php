<?php

namespace App\Controller\Admin;

use App\Entity\ZoneLivraison;
use App\Form\ZoneLivraisonType;
use App\Repository\ZoneLivraisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/zone-livraison')]
class ZoneLivraisonController extends AbstractController
{
    #[Route('/', name: 'admin_zone_livraison_index', methods: ['GET'])]
    public function index(ZoneLivraisonRepository $zoneLivraisonRepository): Response
    {
        return $this->render('admin/zone_livraison/index.html.twig', [
            'zones' => $zoneLivraisonRepository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'admin_zone_livraison_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $zoneLivraison = new ZoneLivraison();
        $form = $this->createForm(ZoneLivraisonType::class, $zoneLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($zoneLivraison);
            $entityManager->flush();

            $this->addFlash('success', 'Zone de livraison créée avec succès.');
            return $this->redirectToRoute('admin_zone_livraison_index');
        }

        return $this->render('admin/zone_livraison/new.html.twig', [
            'zone_livraison' => $zoneLivraison,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_zone_livraison_show', methods: ['GET'])]
    public function show(ZoneLivraison $zoneLivraison): Response
    {
        return $this->render('admin/zone_livraison/show.html.twig', [
            'zone_livraison' => $zoneLivraison,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_zone_livraison_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ZoneLivraison $zoneLivraison, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ZoneLivraisonType::class, $zoneLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Zone de livraison modifiée avec succès.');
            return $this->redirectToRoute('admin_zone_livraison_index');
        }

        return $this->render('admin/zone_livraison/edit.html.twig', [
            'zone_livraison' => $zoneLivraison,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_zone_livraison_delete', methods: ['POST'])]
    public function delete(Request $request, ZoneLivraison $zoneLivraison, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$zoneLivraison->getId(), $request->request->get('_token'))) {
            // Vérifier si la zone a des commandes associées
            if ($zoneLivraison->getNombreCommandes() > 0) {
                $this->addFlash('error', 'Impossible de supprimer cette zone car elle a des commandes associées.');
                return $this->redirectToRoute('admin_zone_livraison_index');
            }

            $entityManager->remove($zoneLivraison);
            $entityManager->flush();
            $this->addFlash('success', 'Zone de livraison supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_zone_livraison_index');
    }
}