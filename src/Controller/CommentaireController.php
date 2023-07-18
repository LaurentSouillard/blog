<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentaireController extends AbstractController
{
    /**
     * @Route("/commentaire_update_{id<\d+>}", name="commentaire_update")
     */
    public function update($id, Request $request, EntityManagerInterface $manager): Response
    {

        $commentaire = $this->getDoctrine()->getRepository(Commentaire::class)->find($id);

        $article =  $commentaire->getArticle();
        
        //si l'utilisateur connecté est diffenrent de l'utilisateur lié au commentaire à mettre à jour
        if ($this->getUser() != $commentaire->getUser()) {
            $this->addFlash("erreur", "accés refusé!");
            return $this->redirectToRoute("article", ["id" => $article->getId()]);
        }


        $form = $this->createForm(CommentaireType::class, $commentaire);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() )
        {
            $manager->persist($commentaire);
            $manager->flush();

            return $this->redirectToRoute('article', ["id" => $article->getId()] );
        }

        return $this->render('article/unArticle.html.twig', [
            'formCommentaire' => $form->createView(),
            'article' => $article,
            'commentaires' => $article->getCommentaires(),
        ]);
    }

    /**
     * @Route("commentaire_delete_{id<\d+>}", name="commentaire_delete")
     */
    public function delete($id, EntityManagerInterface $manager)
    {
        $commentaire = $this->getDoctrine()->getRepository(Commentaire::class)->find($id);

        $article = $commentaire->getArticle();

         //si l'utilisateur connecté est diffenrent de l'utilisateur lié au commentaire à mettre à jour
         if ($this->getUser() != $commentaire->getUser()) {
            $this->addFlash("erreur", "accés refusé!");
            return $this->redirectToRoute("article", ["id" => $article->getId()]);
        }
        
        $manager->remove($commentaire);
        $manager->flush();

        return $this->redirectToRoute('article', ["id" => $article->getId()]);

    }

}
