<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Delivery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DeliveryTypeForm extends AbstractType
{
     public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('order', EntityType::class, [
                'class' => Order::class,
                'choice_label' => function (Order $order) {
                    // Adjust this to match your Order entity structure
                    return $order->getId() . ' - $' . number_format($order->getTotalAmount(), 2);
                },
                'label' => 'Order',
                'placeholder' => 'Select an order',
                'attr' => [
                    'class' => 'form-select',
                ],
                'required' => true,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Processing' => 'processing',
                    'Shipped' => 'shipped',
                    'In Transit' => 'in_transit',
                    'Out for Delivery' => 'out_for_delivery',
                    'Delivered' => 'delivered',
                    'Failed' => 'failed',
                    'Returned' => 'returned'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('delivery_date', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('tracking_number', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Delivery::class,
        ]);
    }
}
