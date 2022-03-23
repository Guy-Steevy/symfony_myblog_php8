<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class, [
                'label' => 'Password', // permet de modifier le nom du champ
                'mapped' => false, // le pwd n'est pas obligatoire a entrer en dtb
                'attr' => [ // attribut d'une balise html
                    'placeholder' => 'Minimum 8 caracters',
                    'class' => 'password'
                ]
            ])
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('datedeNaissance', BirthdayType::class, [
                'label' => 'Date of birth',
                'widget' => 'single_text'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Sign Up',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
