<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitFormType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="liste_produits")
     * @param ProduitRepository $produitRepository
     * @return Response
     */
    public function list(ProduitRepository $produitRepository): Response
    {
        // Récupérer tous les produits
        $liste_produits = $produitRepository->findAll();

        // Envoyer les produits à la vue
        return $this->render('home/liste.html.twig', [
            'liste_produits' => $liste_produits
        ]);
    }

    /**
     * @Route("/{id}/edit", name="modifier_produit")
     * @param Produit $produit
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     */
    public function edit(Produit $produit, EntityManagerInterface $em, Request $request): Response
    {
        // Créer le formulaire
        $form = $this->createForm(ProduitFormType::class, $produit);
        $form->handleRequest($request);
        // Validation formulaire soumis
        if ($form->isSubmitted() && $form->isValid() ) {
            // Enregistrer les modifications dans la bdd
            $em->flush();
            // Rajouter un message flash
            $this->addFlash('success','Le produit a été modifié');
            // Rediriger vers la page de produits
            return $this->redirectToRoute('liste_produits');
        }

        // Envoyer le formulaire à la vue
        return $this->render('home/edit.html.twig', [
            'produitForm' => $form->createView()
        ]);
    }
}
