<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExoController extends AbstractController
{
  
    public function index(): Response
    {
        $identites = [ ['prenom' => 'Hocine', 'nom' => 'BOUSSAID'] , ['prenom' => 'Aniéla', 'nom' => 'BENARD'] ];

        // echo $identite['prenom'];

        return $this->render('exo/index.html.twig', [
            'identites' => $identites,
        ]);
    }
}
