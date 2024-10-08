<?php
namespace App\Form;

use App\Entity\Crypto;
use App\Entity\User;
use App\Entity\Wallet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class WalletType2 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('quantity', NumberType::class, [
            'required' => false, 
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a quantity.',
                ]),
            ],
        ])
        ->add('cryptos', EntityType::class, [
            'class' => Crypto::class,
            'choice_label' => 'name',
        ])
        ->add('users', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'email', 
        ])
        
           ;
          
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wallet::class,
        ]);
    }
}
