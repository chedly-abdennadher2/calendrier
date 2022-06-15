<?php

namespace App\Form;

use App\Entity\Employe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [

                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez un nom'
                ]
            ] )
            ->add('prenom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez un prenom'
                ]
            ])

            ->add('rechercher', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employe::class,
        ]);
    }
}
