<?php
namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'admin_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository, Request $request): Response
    {
        $statut = $request->query->get('statut');
        $date = $request->query->get('date');

        if ($statut || $date) {
            $commandes = $commandeRepository->findWithFilters($statut, $date);
        } else {
            $commandes = $commandeRepository->findBy([], ['date' => 'DESC']);
        }

        return $this->render('admin/commande/index.html.twig', [
            'commandes' => $commandes,
            'filtres' => ['statut' => $statut, 'date' => $date],
        ]);
    }

    #[Route('/{id}', name: 'admin_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('admin/commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/update-status', name: 'admin_commande_update_status', methods: ['POST'])]
    public function updateStatus(
        Commande $commande, 
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $nouveauStatut = $request->request->get('statut');
        $commande->setStatut($nouveauStatut);
        $entityManager->flush();

        $this->addFlash('success', 'Statut de la commande mis à jour.');
        return $this->redirectToRoute('admin_commande_show', ['id' => $commande->getId()]);
    }
}