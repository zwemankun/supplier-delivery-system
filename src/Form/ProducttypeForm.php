<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Supplier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProducttypeForm extends AbstractType
{
  
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter product name'],
                'label_attr' => ['class' => 'form-label'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a product name']),
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 5],
                'label_attr' => ['class' => 'form-label'],
                'required' => false,
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'USD',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'html5' => true,
                'scale' => 2,
            ])
            ->add('quantity', NumberType::class, [
                'attr' => ['class' => 'form-control', 'min' => 0],
                'label_attr' => ['class' => 'form-label'],
                'html5' => true,
                'required' => true,
            ])
       
            ->add('supplier', EntityType::class, [
                'class' => Supplier::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a supplier',
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'required' => false,
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
