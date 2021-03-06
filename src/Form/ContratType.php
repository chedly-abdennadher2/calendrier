<?php

namespace App\Form;

use App\Entity\Contrat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('datedebut')
            ->add('datefin')
            ->add('typedecontrat')
            ->add('statut')

            ->add('employe_id',TextType::class,array('mapped'=>false,'disabled'=>true))
            ->add('employe_nom',TextType::class,array('mapped'=>false,'disabled'=>true))
            ->add('employe_prenom',TextType::class,array('mapped'=>false,'disabled'=>true))

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}
