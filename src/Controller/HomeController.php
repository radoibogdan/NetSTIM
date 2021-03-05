<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitFormType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

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
            'produit' => $produit,
            'produitForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit_in_ajax", name="modifier_produit_ajax")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Produit $produit
     * @return JsonResponse
     */
    public function editInAjax(Request $request, EntityManagerInterface $manager, Produit $produit, CsrfTokenManagerInterface $csrfTokenManager) {
        // Transformer en array les données JSON
        $data = json_decode($request->getContent(), true);
        // Récupérer les valeurs

        $name = $data['name'];
        $description = $data['description'];
        $short_description = $data['short_description'];
        $price = $data['price'];

        // Créer le formulaire pour la validation des données récupérées
        $form = $this->createForm(ProduitFormType::class, $produit);
        $form->handleRequest($request);
        $form->submit($data);
        // Si le formulaire n'est pas valide renvoyer une erreur
        if ($form->isValid() === false) {
            return $this->json([
                    'message' => $this->getErrorMessages($form),
                    'data' => $data
                ],400
            );
        }

        // Modifier le produit
        $produit->setName($name);
        $produit->setDescription($description);
        $produit->setShortDescription($short_description);
        $produit->setPrice($price);
        // Modifier le produit en base de données
        $manager->flush();

        // Renvoyer la réponse JSON
        return $this->json([
            'message' => 'Le produit a été modifié'
        ], 200);

    }

    // Générer un array - clé => valeur avec les erreurs (la clé = nom du champ du formulaire)
    protected function getErrorMessages(\Symfony\Component\Form\FormInterface $form)
    {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
}
