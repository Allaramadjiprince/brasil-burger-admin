<?php
namespace App\Controller\Admin;

use App\Entity\Livreur;
use App\Form\LivreurType;
use App\Repository\LivreurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/livreur')]
class LivreurController extends AbstractController
{
    #[Route('/', name: 'admin_livreur_index', methods: ['GET'])]
    public function index(LivreurRepository $livreurRepository): Response
    {
        return $this->render('admin/livreur/index.html.twig', [
            'livreurs' => $livreurRepository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'admin_livreur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livreur = new Livreur();
        $form = $this->createForm(LivreurType::class, $livreur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livreur);
            $entityManager->flush();

            $this->addFlash('success', 'Livreur ajouté avec succès.');
            return $this->redirectToRoute('admin_livreur_index');
        }

        return $this->render('admin/livreur/new.html.twig', [
            'livreur' => $livreur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_livreur_show', methods: ['GET'])]
    public function show(Livreur $livreur): Response
    {
        return $this->render('admin/livreur/show.html.twig', [
            'livreur' => $livreur,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_livreur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livreur $livreur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivreurType::class, $livreur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Livreur modifié avec succès.');
            return $this->redirectToRoute('admin_livreur_index');
        }

        return $this->render('admin/livreur/edit.html.twig', [
            'livreur' => $livreur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/toggle-disponibilite', name: 'admin_livreur_toggle_disponibilite', methods: ['POST'])]
    public function toggleDisponibilite(Livreur $livreur, EntityManagerInterface $entityManager): Response
    {
        $livreur->setDisponible(!$livreur->isDisponible());
        $entityManager->flush();

        $this->addFlash('success', 'Disponibilité du livreur mise à jour.');
        return $this->redirectToRoute('admin_livreur_index');
    }

    #[Route('/{id}', name: 'admin_livreur_delete', methods: ['POST'])]
    public function delete(Request $request, Livreur $livreur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livreur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livreur);
            $entityManager->flush();
            $this->addFlash('success', 'Livreur supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_livreur_index');
    }
}