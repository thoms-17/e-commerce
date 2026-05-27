<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr'  => ['placeholder' => 'ex : iPhone 16'],
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug (URL)',
                'attr'  => ['placeholder' => 'ex : iphone-16'],
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
                'attr'     => ['rows' => 4, 'placeholder' => 'Description du produit…'],
            ])
            ->add('price', TextType::class, [
                'label' => 'Prix (€)',
                'attr'  => ['placeholder' => '0.00'],
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock',
                'attr'  => ['min' => 0],
            ])
            ->add('image', TextType::class, [
                'label'    => 'Image (URL)',
                'required' => false,
                'attr'     => ['placeholder' => 'https://…'],
            ])
            ->add('category', EntityType::class, [
                'class'        => Category::class,
                'choice_label' => 'name',
                'label'        => 'Catégorie',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
