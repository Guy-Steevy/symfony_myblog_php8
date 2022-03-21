<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
 public function __construct(EntityManagerInterface $manager){
       $this->manager = $manager;
 }

    #[Route('/article', name: 'app_article')]
    public function index(Request $request): Response
    {
           $article = new Article(); // Nouvelle instance de article
           $form = $this->createForm(ArticleType::class,$article); // Création du formulaire
           $form->handleRequest($request); // Traitement du formulaire
           if($form->isSubmitted() && $form->isValid()){ 
               $this->manager->persist($article);
               $this->manager->flush();
               return $this->redirectToRoute('app_home');
           };
            
        return $this->render('article/index.html.twig', [
            'formArticle' => $form->createView(),
        ]);
    }



    #[Route('/article/delete/{id}', name: 'app_article_delete')]
    public function articleDelete(Article $article): Response
    {
        $this->manager->remove($article);
        $this->manager->flush();
        return $this->redirectToRoute('app_home');

    }



    #[Route('/article/edit/{id}', name: 'app_article_edit')]
    public function articleEdit(Article $article): Response
    {
        



        
        return $this->redirectToRoute('app_home');

    }
}
