<?php

namespace App\Form;

use App\Entity\Conge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CongeSearchFormulaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mois', TextType::class, [
                'label' => false,
                'mapped'=>false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez un mois'
                ]
            ])
            ->add('annee', TextType::class, [
                'label' => false,
                'mapped'=>false,
                    'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez un annee'
                ]
            ] )
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conge::class,
        ]);
    }
}
