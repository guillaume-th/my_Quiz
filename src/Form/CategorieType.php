<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Question;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
            );

        for ($i = 0; $i < 10; $i++) {
            $builder->add("categorie", CollectionType::class, [
                "entry_type" => QuestionType::class,
                "entry_options" => ["label" => false, "attr" => ["class" => "question-div"]],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
