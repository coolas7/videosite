<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $user = $options['user'];

        $builder
            ->add('name', TextType::class,[
                'required' => true,
                'constraints' => [new Length(['min' => 3])],
                'empty_data' => '',
            ])
            ->add('last_name', TextType::class,[
                'required' => true,
                'constraints' => [new Length(['min' => 3])],
                'empty_data' => '',
            ])
            ->add('email', EmailType::class,[
                'required' => true,
                'constraints' => [new Length(['min' => 5])],
                'empty_data' => '',
            ])
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
            ])
        ;

        if ( $user && in_array('ROLE_ADMIN', $user->getRoles()) )
        {

            $builder->add('vimeo_api_key', TextType::class,[
                'empty_data' => '',
            ]);

        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'user' => null,
        ]);
    }
}
