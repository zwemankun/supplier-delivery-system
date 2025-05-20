<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class OrderTypeForm extends AbstractType
{
     public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'label' => 'Customer',
                'mapped' => false, // Not directly mapped to Order entity
                'choice_label' => function (Customer $customer) {
                    return $customer->getId() . ' - ' . $customer->getName();
                },
                'placeholder' => 'Select a customer',
                'attr' => [
                    'class' => 'form-select',
                ],
                'required' => true,
            ])
            ->add('total_amount', MoneyType::class, [
                'label' => 'Total Amount',
                'currency' => 'USD',
                'mapped' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0.00'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'mapped' => true,
                'choices' => [
                    'Pending' => 'pending',
                    'Processing' => 'processing',
                    'Shipped' => 'shipped',
                    'Delivered' => 'delivered',
                    'Cancelled' => 'cancelled'
                ],
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => $options['is_edit'] ? 'Update Order' : 'Create Order',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'is_edit' => false,
        ]);
    }
}