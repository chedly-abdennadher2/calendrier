<?php

namespace App\Form;

use App\Entity\Conge;

use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Date;

class CongeValiderType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $choices = [
            'oui' => 'oui',
            'non' => 'non'
        ];
        $builder
            ->add ('id',TextType::class,['mapped'=>false,'disabled'=>true])

            ->add ('choix', ChoiceType::class, [
        'choices' => $choices,
       'expanded' => true,
        'multiple'=>false,
        'mapped'=>false,// => boutons
         'label' => 'choix'
            ])
            ->add('datedebut',null,['disabled'=>true])
            ->add('datefin',null,['disabled'=>true])
            ->add('typeconge',null,['disabled'=>true])

            ->add ('id_user',TextType::class,['mapped'=>false,'disabled'=>true])
            ->add ('nom',TextType::class,['mapped'=>false,'disabled'=>true])
            ->add ('prenom',TextType::class,['mapped'=>false,'disabled'=>true])

            ->add ('valider',SubmitType::class )
            ->add ('annuler',ResetType::class )

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conge::class,
        ]);
    }
}
