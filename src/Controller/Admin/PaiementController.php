<?php
namespace App\Controller\Admin;

use App\Entity\Paiement;
use App\Repository\PaiementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/paiement')]
class PaiementController extends AbstractController
{
    #[Route('/', name: 'admin_paiement_index', methods: ['GET'])]
    public function index(PaiementRepository $paiementRepository, Request $request): Response
    {
        $statut = $request->query->get('statut');
        
        if ($statut && in_array($statut, [Paiement::STATUT_EN_ATTENTE, Paiement::STATUT_PAYE, Paiement::STATUT_ECHEC])) {
            $paiements = $paiementRepository->findBy(['statut' => $statut], ['date' => 'DESC']);
        } else {
            $paiements = $paiementRepository->findAllOrderByDate();
        }

        return $this->render('admin/paiement/index.html.twig', [
            'paiements' => $paiements,
            'filtre_statut' => $statut,
        ]);
    }

    #[Route('/{id}', name: 'admin_paiement_show', methods: ['GET'])]
    public function show(Paiement $paiement): Response
    {
        return $this->render('admin/paiement/show.html.twig', [
            'paiement' => $paiement,
        ]);
    }

    #[Route('/{id}/valider', name: 'admin_paiement_valider', methods: ['POST'])]
    public function valider(Paiement $paiement, EntityManagerInterface $entityManager): Response
    {
        if ($paiement->getStatut() === Paiement::STATUT_EN_ATTENTE) {
            $paiement->setStatut(Paiement::STATUT_PAYE);
            $entityManager->flush();
            $this->addFlash('success', 'Paiement validé avec succès.');
        }

        return $this->redirectToRoute('admin_paiement_index');
    }

    #[Route('/{id}/refuser', name: 'admin_paiement_refuser', methods: ['POST'])]
    public function refuser(Paiement $paiement, EntityManagerInterface $entityManager): Response
    {
        if ($paiement->getStatut() === Paiement::STATUT_EN_ATTENTE) {
            $paiement->setStatut(Paiement::STATUT_ECHEC);
            $entityManager->flush();
            $this->addFlash('warning', 'Paiement refusé.');
        }

        return $this->redirectToRoute('admin_paiement_index');
    }
}