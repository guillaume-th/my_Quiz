<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextType::class, ["attr" => ["class"=>"quest-single-div"]]);
        $builder->add("reponse", CollectionType::class, [
            "entry_type" => ReponseType::class,
            "entry_options" => ["label" => false, "attr" => ["class" => "reponse-div"]]
        ]);
        $builder->add("reponse", CollectionType::class, [
            "entry_type" => ReponseType::class,
            "entry_options" => ["label" => false,"attr" => ["class" => "reponse-div"]]
        ]);
        $builder->add("reponse", CollectionType::class, [
            "entry_type" => ReponseType::class,
            "entry_options" => ["label" => false, "attr" => ["class" => "reponse-div"]]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
