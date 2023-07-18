<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Auteur;
use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 10; $i++) { 
            
            // $article = new Article();

            // $article->setTitre("mon article $i")
            //         ->setContenu("$i Lorem ipsum dolor sit amet consectetur adipisicing elit. Officiis debitis, dolorem laborum, veritatis quaerat consequatur aperiam dolore doloremque, hic quod qui excepturi exercitationem harum iste aspernatur! Architecto nam perferendis iure.")
            //         ->setDateDeCreation(new DateTime("now")) ;
            // $manager->persist($article);                    

            $auteur = new Auteur();

            $auteur->setNom("nom$i")
                    ->setPrenom("prenom$i")
                    ->setBiographie("ma biographie $i")
                    ->setDateDeNaissance(new DateTime("now"));
            $manager->persist($auteur);
        }

        $manager->flush();
    }
}
