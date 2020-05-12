<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $user = $options["data"]->getRoles();
        // var_dump($options["data"]->getRoles());die;
        $builder
                ->add('email')
                ->add('roles',ChoiceType::class,[
                    'required' => false,
                    'choices'  => [
                        'ROLE_USER' => 'ROLE_USER',
                        'ROLE_ADMIN' => 'ROLE_ADMIN'
                    ],
                    'multiple' => false,
                    'expanded' => true
                ])
                // ->add('password')
                ->add('valider',SubmitType::class)
                ->get('roles')
                ->addModelTransformer(new CallbackTransformer(
                    function ($rolesAsArray) {
                        // transform the array to a string
                        return implode(', ', $rolesAsArray);
                    },
                    function ($rolesAsString) {
                        // transform the string back to an array
                        return explode(', ', $rolesAsString);
                    }
                ));
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
