<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CustomerTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('uniqueID', TextType::class, [
                'attr' => ['placeholder' => 'Unique ID'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a unique ID',
                    ]),
                ],
            ])
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Full Name'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a name',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Email Address'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email',
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email',
                    ]),
                ],
            ])
            ->add('phone', TelType::class, [
                'attr' => ['placeholder' => 'Phone Number'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a phone number',
                    ]),
                ],
            ])
            ->add('address', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Address', 
                    'rows' => 3
                ],
                'required' => false,
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
