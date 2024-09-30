<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your first name.',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your last name.', 
                    ]),
                ],
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'required' => false, 
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email.', 
                    ]),
                    new Email([
                        'message' => 'The email "{{ value }}" is not a valid email.', 
                    ]),
                ],
            ])
            ->add('Confirm', CheckboxType::class, [
                'mapped' => false,
                'required' => false, 
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'required' => false, 
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password.', 
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
