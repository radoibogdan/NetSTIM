<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Configurer faker
        $faker = Factory::create('fr_FR');

        // Créer 20 produits
        for ($i = 0; $i < 20; $i++) {
            $produit = new Produit();
            $produit
                ->setName($faker->sentence(2))
                ->setShortDescription($faker->realText(50))
                ->setDescription($faker->realText(200))
                ->setPrice($faker->numberBetween(0,100))
            ;
            // persister le produit
            $manager->persist($produit);
        }
        // Un seul flush pour minimiser les requêtes SQL
        $manager->flush();
    }
}
