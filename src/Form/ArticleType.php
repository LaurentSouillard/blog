<?php

namespace App\Form;

use App\Entity\Tags;
use App\Entity\Auteur;
use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('contenu', CKEditorType::class)
            ->add('imageForm', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'image',
            ])
            ->add('auteur', EntityType::class, [
                'placeholder' => 'choisissez un auteur',
                'class' => Auteur::class,
                'choice_label' => 'fullName'
            ])
            ->add('categorie', EntityType::class, [
                'placeholder' => 'choisissez une categorie',
                'class' => Categorie::class,
                'choice_label' => 'nom'
            ])
            ->add('tags', EntityType::class, [
                'class' => Tags::class,
                'label' => "Cochez un ou plusieurs tags",
                'choice_label' => 'label',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('envoyer', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
