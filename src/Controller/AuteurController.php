<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Form\AuteurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuteurController extends AbstractController
{
    /**
     * @Route("/auteurs", name="auteurs")
     */
    public function allAuteurs(): Response
    {
        $auteurs = $this->getDoctrine()->getRepository(Auteur::class)->findAll();

        //dd($auteurs);

        return $this->render('auteur/lesAuteurs.html.twig', [
            'auteurs' => $auteurs
        ]);
    }

    /**
     * @Route("/auteur/{id<\d+>}", name="auteur")
     */
    public function unAuteur($id)
    {
        $auteur = $this->getDoctrine()->getRepository(Auteur::class)->find($id);

        //dd($auteur);

        return $this->render('auteur/unAuteur.html.twig', [
            'auteur' => $auteur
        ]);
    }

    /**
     * @Route("auteur/ajout", name="auteur_ajout")
     */
    public function ajout(EntityManagerInterface $manager, Request $request)
    {
         // on verifie si l'utilisateur est connecté
         if( !$this->isGranted('IS_AUTHENTICATED_FULLY') )
         {
             $this->addFlash("erreur", "veuillez vous connecter avant de consulter cette page ! ! !");
 
             return $this->redirectToRoute('app_login');
         }
 
         // l'utilateur est connecté, on verifie maintenant si son role est ADMIN
         if( !$this->isGranted("ROLE_ADMIN"))
         {
             $this->addFlash("erreur", "vous n'êtes pas autorisé à accéder à cette page, veullez contacter l'administrateur du site !");
 
             return $this->redirectToRoute("home");
         } 
        $auteur = new Auteur();

        $form = $this->createForm(AuteurType::class, $auteur);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($auteur);
            $manager->flush();

            return $this->redirectToRoute('auteurs');
        }

        return $this->render('auteur/formulaire.html.twig', [
            "formAuteur" => $form->createView()
        ]);

    }

    /**
     * @Route("auteur/update_{id<\d+>}", name="auteur_update")
     */
    public function update($id, Request $request, EntityManagerInterface $manager)
    {
         // on verifie si l'utilisateur est connecté
         if( !$this->isGranted('IS_AUTHENTICATED_FULLY') )
         {
             $this->addFlash("erreur", "veuillez vous connecter avant de consulter cette page ! ! !");
 
             return $this->redirectToRoute('app_login');
         }
 
         // l'utilateur est connecté, on verifie maintenant si son role est ADMIN
         if( !$this->isGranted("ROLE_ADMIN"))
         {
             $this->addFlash("erreur", "vous n'êtes pas autorisé à accéder à cette page, veullez contacter l'administrateur du site !");
 
             return $this->redirectToRoute("home");
         }

        $auteur = $this->getDoctrine()->getRepository(Auteur::class)->find($id);

        $form = $this->createForm(AuteurType::class, $auteur);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($auteur);
            $manager->flush();

            return $this->redirectToRoute("auteurs");
        }

        return $this->render('auteur/formulaire.html.twig', [
            'formAuteur' => $form->createView()
        ]);
    }


    /**
     * @Route("/auteur_delete_{id<\d+>}", name="auteur_delete")
     */
    public function delete($id, EntityManagerInterface $manager)
    {
         // on verifie si l'utilisateur est connecté
         if( !$this->isGranted('IS_AUTHENTICATED_FULLY') )
         {
             $this->addFlash("erreur", "veuillez vous connecter avant de pouvoir réaliser cette action ! ! !");
 
             return $this->redirectToRoute('app_login');
         }
 
         // l'utilateur est connecté, on verifie maintenant si son role est ADMIN
         if( !$this->isGranted("ROLE_ADMIN"))
         {
             $this->addFlash("erreur", "vous n'êtes pas autorisé à réaliser cette action, veuillez contacter l'administrateur du site !");
 
             return $this->redirectToRoute("home");
         } 
        $auteur = $this->getDoctrine()->getRepository(Auteur::class)->find($id);

        $manager->remove($auteur);
        $manager->flush();

        return $this->redirectToRoute('auteurs');
    }
}
