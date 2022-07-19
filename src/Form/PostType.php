<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre',
                'help' => 'Définir un titre précis et non-ambigüe.'
            ])
            ->add('body', TextareaType::class, [
                'attr' => [
                    'cols' => 60,
                    'rows' => 15,
                ],
            ])
            ->add('categorizedBy', EntityType::class, [
                'placeholder' => '-',
                'required' => false,
                'choice_label' => 'name',
                'class' => Category::class,
            ])
        ;

        if ($options['with_creation']) {
            $builder->add('createdAt');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'with_creation' => false,
        ]);
    }
}
