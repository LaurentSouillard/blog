<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    /**
     * @Route("/categorie_ajout", name="categorie_ajout")
     */
    public function ajout(Request $request, SluggerInterface $slugger, EntityManagerInterface $manager): Response
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

        $categorie = new Categorie();

        
        $form = $this->createForm(CategorieType::class, $categorie);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            // on crée le slug du nom de la categorie 
            $slug = $slugger->slug($categorie->getNom());

            $categorie->setSlug($slug);

            $manager->persist($categorie);
            $manager->flush();

            return $this->redirectToRoute('categories');
           
        }

        return $this->render('categorie/formulaire.html.twig', [
            'formCategorie' => $form->createView(),
        ]);
    }


    /**
     * @Route("/categories", name="categories")
     */
    public function allCategories()
    {

        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();

        return $this->render('categorie/allCategories.html.twig', [
            'categories' => $categories
        ]); 

    }



    /**
     * @Route("/categorie_update_{id<\d+>}", name="categorie_update")
     */
    public function update($id, Request $request, SluggerInterface $slugger, EntityManagerInterface $manager)
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

        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($id);

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $slug = $slugger->slug($categorie->getNom());

            $categorie->setSlug($slug);

            $manager->persist($categorie);
            $manager->flush();
            
            return $this->redirectToRoute('categories');
        }


        return $this->render('categorie/formulaire.html.twig', [ 
            'formCategorie'  => $form->createView()
    ]);
    }


    /**
     * @Route("/categorie_delete_{id<\d+>}", name="categorie_delete")
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
             $this->addFlash("erreur", "vous n'êtes pas autorisé à réaliser cette page, veuillez contacter l'administrateur du site !");
 
             return $this->redirectToRoute("home");
         } 
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($id);
    
        $manager->remove($categorie);
        $manager->flush();

        return $this->redirectToRoute('categories');
    
    }


    /**
     * @Route("/categorie/{slug}", name="categorie_articles")
     */
    public function articlesCategorie($slug, PaginatorInterface $paginator, Request $request)
    {
        // on récupere la categorie dont le slug est celui passé dans l'url (on utilise pas le find() car c'est uliquement pour l'id)
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->findOneBy(['slug' => $slug] );

        // on recupere les article liés à la categorie recuperé ci dessus
        $donnees = $categorie->getArticles();

        // Pagination
        $articles = $paginator->paginate(
            $donnees, // Requete contenant les données à paginer (ici nos articles $donnees)
            $request->query->getInt('page', 1) , // Numéro de la page en cours passé dans l'url, 1 si aucune page
            2 // nombre de résultats par page
        );


        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();

        return $this->render('article/lesArticles.html.twig', [
            'articles' => $articles,
            'categories' => $categories
        ]);

    }

}
