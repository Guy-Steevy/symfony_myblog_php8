<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {

        $users = $this->manager->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }


    #[Route('/user/edit/{id}', name: 'app_user_edit')]
    public function userEdit(User $user, Request $request): Response
    {
        $form = $this->createForm(RegisterType::class, $user); // Création du formulaire
        $form->handleRequest($request); // Traitement du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            $emptyPassword = $form->get('password')->getData();

            // condition : si le champ du mot de passe est vide, récupère celui qui est dans la base de données
            if ($emptyPassword == null) {
                $user->setPassword($user->getPassword());
            }

            $this->manager->persist($user);
            $this->manager->flush();
            return $this->redirectToRoute('app_user');
        };

        return $this->render('user/editUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
