<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstTestController extends AbstractController
{
    /**
     * @Route("/first/test", name="first_test")
     */
    public function index(): Response
    {
        $prenom = "Hocine";

        return $this->render("test/test.html.twig" , [
            'prenom' => $prenom
        ]);
    }
    
}
