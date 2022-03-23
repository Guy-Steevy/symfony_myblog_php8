<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
   public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHash)
   {
      $this->manager = $manager;
      $this->passwordHash = $passwordHash;
   }

   /**
    * @Route("/register", name="app_register")
    */
   public function index(Request $request): Response
   {
      $user = new User(); // nouvelle instance de ma classe User
      $form = $this->createForm(RegisterType::class, $user); // création du formulaire
      $form->handleRequest($request); // traitement du formulaire

      if ($form->isSubmitted() && $form->isValid()) { // si le formulaire et soumis et valide alors...
         $emptyPassword = $form->get('password')->getData();

         if ($emptyPassword == null) {
            $user->setPassword($user->getPassword());
         } else {
            $passwordEncod =  $this->passwordHash->hashPassword($user, $emptyPassword);
            $user->setPassword($passwordEncod);
         }

         $this->manager->persist($user); // pour préparer l'envoi en base de données
         $this->manager->flush(); // on flush (envoi les données)
         return $this->redirectToRoute('app_login'); // renvoi vers la page de connexion
      }

      return $this->render('register/index.html.twig', [
         'myForm' => $form->createView() // On passe le formulaire à la vue
      ]);
   }
}
