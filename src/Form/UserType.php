<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => true,  
                'required' => false, 
                'attr' => [
                    'placeholder' => 'Email' 
                ],
                'constraints' => [
                    new NotBlank([
                       'message' => 'Please enter your email.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false, 
                'label' => false, 
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Password' 
                ],
            ])
            ->add('solde', TextType::class, [
                'label' => false, 
                'required' => false, 
                'attr' => [
                    'placeholder' => 'Balance'  
                ],
            ])
            ->add('first_name', TextType::class, [
                'label' => false, 
                'required' => false, 
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your first name.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'First Name' 
                ],
            ])
            ->add('last_name', TextType::class, [
                'label' => false, 
                'required' => false, 
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your last name.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Last Name'  
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
