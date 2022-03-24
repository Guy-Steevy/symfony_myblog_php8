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
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/admin/article', name: 'app_article')]
    public function index(Request $request): Response
    {
        $article = new Article(); // Nouvelle instance de article
        $form = $this->createForm(ArticleType::class, $article); // Création du formulaire
        $form->handleRequest($request); // Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // recuperer l'utilisateur connecter et envoyer le prenom dans le setAuteur.

            $article->setAuteur($this->getUser()->getPrenom());

            $this->manager->persist($article);
            $this->manager->flush();
            return $this->redirectToRoute('app_home'); // renvoi vers la page d'acceuil
        };

        return $this->render('article/index.html.twig', [
            'formArticle' => $form->createView(),
        ]);
    }



    #[Route('/admin/article/delete/{id}', name: 'app_article_delete')]
    public function articleDelete(Article $article): Response
    {
        $this->manager->remove($article);
        $this->manager->flush();
        return $this->redirectToRoute('app_home');
    }



    #[Route('/admin/article/edit/{id}', name: 'app_article_edit')]
    public function articleEdit(Article $article, Request $request): Response
    {
        $form = $this->createForm(ArticleType::class, $article); // Création du formulaire
        $form->handleRequest($request); // Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($article);
            $this->manager->flush();
            return $this->redirectToRoute('app_home');
        };

        return $this->render('article/editArticle.html.twig', [
            'formArticle' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/allarticle", name="app_all_article")
     */
    public function allArticle(): Response
    {
        $articles = $this->manager->getRepository(Article::class)->findAll();
        // logique stocker dans une variable avec tout les articles

        return $this->render('home/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/single/article", name="app_single_article")
     */
    public function singleArticle(): Response
    {
        $article = $this->manager->getRepository(Article::class)->findAll();
        // logique stocker dans une variable avec tout les articles

        return $this->render('article/singleArticle.html.twig', [
            'articles' => $article,
        ]);
    }
}
