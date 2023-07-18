<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Form\ArticleType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/articles", name="articles")
     */
    public function allArticles(PaginatorInterface $paginator, Request $request): Response
    {
        $profiler = new Profiler();
        $profiler->disable();

        $donnees = $this->getDoctrine()->getRepository(Article::class)->findAll();
        //dd($articles);

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

    /** 
     *@Route("/article_{id<\d+>}" , name="article")
    */
    public function unArticle($id, Request $request, EntityManagerInterface $manager)// $id recupére la valeur de l'id depuis la route
    {
        // on cherche l'article dans la bdd dont l'id est celui passé en parametre de la route
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        //dd($article);

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
                $this->addFlash('erreur', 'Veuillez vous connecter pour pouvoir laisser un commentaire');
                return $this->redirectToRoute('app_login');
            }

            $commentaire->setDate(new Datetime('now'));
            $commentaire->setArticle($article);
            //on récupere l'utilisateur connecté
            $user = $this->getUser();
            $commentaire->setUser($user);

            $manager->persist($commentaire);
            $manager->flush();

            $this->addFlash('success', "merci d'avoir laisser un commentaire");

            return $this->redirectToRoute('article', ["id" => $id]);

        }

        return $this->render('article/unArticle.html.twig', [
            'article' => $article,
            'commentaires' => $article->getCommentaires(),
            'formCommentaire' => $form->createView()
        ]);
    }


    /**
     * @Route("/article_ajout", name="article_ajout")
     */
     public function ajout(EntityManagerInterface $manager, Request $request, SluggerInterface $slugger)
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

         $article = new Article();

         $form = $this->createForm(ArticleType::class, $article);

         $form->handleRequest($request);

         if( $form->isSubmitted() && $form->isValid() )
         {
             $article->setDateDeCreation(new DateTime("now"));


             // on recupere l'image du formulaire
             $file = $form->get('imageForm')->getData();

             //dd($file);

             $fileName = $slugger->slug($article->getTitre()) . uniqid() . '.' . $file->guessExtension();
                //dd($fileName);

              try{
                  $file->move($this->getParameter('article_image'), $fileName);
              }
              catch(FileExeption $e)
              {
                  // gérer les exceptions en cas d'erreur durant l'upload 
              }

             $article->setImage($fileName);

             $manager->persist($article);

             $manager->flush();

             return $this->redirectToRoute('articles');
         }

         return $this->render('article/formulaire.html.twig', [
             'formArticle' => $form->createView()
         ]);
     }

     /**
      * @Route("/article_update_{id<\d+>}", name="article_update")
      */
      public function update($id, EntityManagerInterface $manager, Request $request, SluggerInterface $slugger)
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
        //on recupére l'article à modifier dont l'id est celui passé dans l'url
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        //dd($article);

        $form = $this->createForm(ArticleType::class, $article);

         $form->handleRequest($request);

         $image = $article->getImage(); 

         if( $form->isSubmitted() && $form->isValid() )
         {
             if($form->get('imageForm')->getData())
             {
                //on recupere l'image du formulaire
                $imageFile = $form->get('imageForm')->getData();
                // on crée un nouveau nom pour l'image
                $fileName = $slugger->slug($article->getTitre()) . uniqid() . '.' . $imageFile->guessExtension();

                //on deplace l'image vers le dossier paramtré dans le service.yaml (article_image)
                try{
                    $imageFile->move($this->getParameter('article_image'), $fileName );
                }catch(FileException $e){
                        // gestion des exceptions
                }

                $article->setImage($fileName);

             }else{
                $article->setImage($image);
             }

             $article->setDateUpdate(new DateTime("now"));

             $manager->persist($article);

             $manager->flush();

             return $this->redirectToRoute('articles');
         }

         return $this->render('article/formulaire.html.twig', [
             'formArticle' => $form->createView()
         ]);

      }


      /**
       * @Route("article_delete_{id<\d+>}", name="article_delete")
       */
       public function delete($id, EntityManagerInterface $manager)
       {
            // on verifie si l'utilisateur est connecté
        if( !$this->isGranted('IS_AUTHENTICATED_FULLY') )
        {
            $this->addFlash("erreur", "veuillez vous connecter afin de realiser cette action ! ! !");

            return $this->redirectToRoute('app_login');
        }

        // l'utilateur est connecté, on verifie maintenant si son role est ADMIN
        if( !$this->isGranted("ROLE_ADMIN"))
        {
            $this->addFlash("erreur", "vous n'êtes pas autorisé à réaliser cette action, veuillez contacter l'administrateur du site !");

            return $this->redirectToRoute("home");
        } 
            // on cherche l'article à supprimer de la bdd avec l'id de l'url
            $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

            $manager->remove($article);

            $manager->flush();

            return $this->redirectToRoute('articles');
       }


}
