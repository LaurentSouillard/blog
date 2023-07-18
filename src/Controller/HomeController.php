<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        // on cherche le dernier article en bdd 
        $dernierArticle = $this->getDoctrine()->getRepository(Article::class)->findOneBy([], ["dateDeCreation" => "DESC"]);

        // on verifie le contenu de la variable 
        //dd($dernierArticle);   
        
        return $this->render('home/index.html.twig', [
            'dernierArticle' => $dernierArticle
        ]);
    }
}
