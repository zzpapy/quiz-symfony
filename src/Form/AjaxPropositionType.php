<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Proposition;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AjaxPropositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text')
            ->add('correct')
            ->add('question',EntityType::class, [
                'class' => Question::class,
                'choice_label' => 'title',
                'expanded' => true
            ])
            ->add('valider',SubmitType::class,[
                'attr' => [
                    'class'=>'submitAjax'
                ]
            ]);
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $proposition = $event->getData();
                $form = $event->getForm();
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Proposition::class,
            'attr' => [
                'novalidate' => 'novalidate', // comment me to reactivate the html5 validation!  🚥
            ],
            
        ]);
    }
}
