<?php
namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'admin_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('admin/produit/index.html.twig', [
            'produits' => $produitRepository->findAllOrderByNom(),
        ]);
    }

    #[Route('/new', name: 'admin_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^A-Za-z0-9_]/', '_', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                
                try {
                    $imageFile->move(
                        $this->getParameter('produits_directory'),
                        $newFilename
                    );
                    
                    $produit->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Produit créé avec succès.');
            return $this->redirectToRoute('admin_produit_index');
        }

        return $this->render('admin/produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('admin/produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image pour l'édition
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^A-Za-z0-9_]/', '_', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                
                try {
                    $imageFile->move(
                        $this->getParameter('produits_directory'),
                        $newFilename
                    );
                    
                    // Supprime l'ancienne image si elle existe
                    if ($produit->getImage()) {
                        $oldImage = $this->getParameter('produits_directory').'/'.$produit->getImage();
                        if (file_exists($oldImage)) {
                            unlink($oldImage);
                        }
                    }
                    
                    $produit->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès.');
            return $this->redirectToRoute('admin_produit_index');
        }

        return $this->render('admin/produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            // Supprime l'image associée
            if ($produit->getImage()) {
                $imagePath = $this->getParameter('produits_directory').'/'.$produit->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $entityManager->remove($produit);
            $entityManager->flush();
            $this->addFlash('success', 'Produit supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_produit_index');
    }
}