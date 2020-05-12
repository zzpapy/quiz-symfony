<?php

namespace App\Form;

use App\Entity\Reponse;
use App\Entity\Question;
use App\Entity\Proposition;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ReponseType extends AbstractType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder;
        $questionnaire = $options["data"]["questionnaire"];
        
        $count = 0;
        $questCount = 0;
       
        foreach ($questionnaire->getQuestions() as  $key => $value) {
            $builder->add("question".$questCount++ , CollectionType::class, [
                "entry_type" => CheckBoxType::class,
                'allow_add' => true,
                "label"   => $value->getTitle()]);
                foreach ($value->getPropositions() as $key => $value) {
                    $builder->add("proposition".(++$count).$questCount, CheckboxType::class, [
                        // "expanded" => true,
                        "label" => $value->getText(),
                        "required" => false,
                        "value"    => $value->getCorrect()
                        // "multiple" => false
                        ]);
                };
        }
        // foreach ($questionnaire->getQuestions() as $value) {
        //     $builder->add("question" . (++$count), ChoiceType::class, [
        //         "choices" => $value->getPropositions(), "expanded" => true,
        //         'choice_label' => function (?Proposition $entity) {
        //             return $entity ? $entity->getId() : '';
        //         },
        //         'choice_value' => function (?Proposition $entity) {
        //             return $entity ? $entity->getCorrect() : '';
        //         },
        //         "label"   => $value->getId(),
        //         "multiple" => true
        //         ]);
        // }
        $builder->add('valider',SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'allow_extra_fields' => true,
        ]);
    }

   
}
