<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ArticleController extends AbstractController
{
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/admin/article', name: 'app_article')]
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $article = new Article(); // Nouvelle instance de article
        $form = $this->createForm(ArticleType::class, $article); // Création du formulaire
        $form->handleRequest($request); // Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            $photoArticle = $form->get('photo')->getData();
            if ($photoArticle) {
                $originalFilename = pathinfo($photoArticle->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoArticle->guessExtension();

                try {
                    $photoArticle->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $article->setPhoto($newFilename);
            }

            // recuperer l'utilisateur connecter et envoyer le prenom dans le setAuteur.
            $article->setAuteur($this->getUser()->getPrenom());
            $article->setPublication(new \DateTime()); // on met la date de publication à la date actuelle
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
    public function articleEdit(Article $article, Request $request, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ArticleType::class, $article); // Création du formulaire
        $form->handleRequest($request); // Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            $photoArticle = $form->get('photo')->getData(); // on récupère la photo
            if ($photoArticle) {
                $originalFilename = pathinfo($photoArticle->getClientOriginalName(), PATHINFO_FILENAME); // on récupère le nom de la photo
                $safeFilename = $slugger->slug($originalFilename); // on crée un slug qui rend le nom plus lisible pour l'être humain
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoArticle->guessExtension(); // on crée un nom unique pour la photo

                try {
                    $photoArticle->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $article->setPhoto($newFilename);
            } else {
                dd('aucune photo');
            }

            $article->setPublication(new \DateTime()); // on met la date de publication à la date actuelle
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
     * @Route("/single/article/{id}", name="app_single_article")
     */
    public function singleArticle(Article $article, Request $request): Response
    {
        /* -----------------------------------------------------------------------------------
         * CREER le formulaire de commentaires et sa logique
         * AFFICHER le formulaire dans la modale
         * lors de la SOUMISSION, envoyer en dtb le commentaire avec les données suivantes :
         * |-> commentary
         * |-> user
         * |-> article
         * |-> date
         */
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setDate(new \DateTime());
            $comment->setUser($this->getUser());
            $comment->setArticle($article);
            $this->manager->persist($comment);
            $this->manager->flush();
            return $this->redirectToRoute('app_single_article', [
                'id' => $article->getId()
            ]);
        }


        return $this->render('article/singleArticle.html.twig', [
            'article' => $article, 'form' => $form->createView()
        ]);
    }
}
