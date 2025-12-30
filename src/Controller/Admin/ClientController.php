<?php
namespace App\Controller\Admin;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/client')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'admin_client_index', methods: ['GET'])]
    public function index(ClientRepository $clientRepository, Request $request): Response
    {
        $search = $request->query->get('search');
        
        if ($search) {
            $clients = $clientRepository->findBySearch($search);
        } else {
            $clients = $clientRepository->findAll();
        }

        return $this->render('admin/client/index.html.twig', [
            'clients' => $clients,
            'search' => $search,
        ]);
    }

    #[Route('/{id}', name: 'admin_client_show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('admin/client/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/{id}/toggle-active', name: 'admin_client_toggle_active', methods: ['POST'])]
    public function toggleActive(Client $client, EntityManagerInterface $entityManager): Response
    {
        $client->setRoles($client->getRoles() === ['ROLE_CLIENT'] ? ['ROLE_CLIENT_BLOCKED'] : ['ROLE_CLIENT']);
        $entityManager->flush();

        $this->addFlash('success', 'Statut du client mis à jour.');
        return $this->redirectToRoute('admin_client_index');
    }
}