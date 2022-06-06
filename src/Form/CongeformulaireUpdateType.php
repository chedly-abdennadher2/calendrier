<?php

namespace App\Form;

use App\Entity\Conge;

use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class CongeformulaireUpdateType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('datedebut')
            ->add('datefin')
            ->add ('id',TextType::class,['mapped'=>false,'disabled'=>true])
            ->add ('envoyer',SubmitType::class )

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conge::class,
        ]);
    }
}
