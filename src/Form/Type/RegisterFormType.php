<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [new Length(max: 15)],
                'required' => true
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [new Length(max: 15)],
                'required' => true
            ])
            ->add('email', TextType::class, [
                'constraints' => [new Length(max: 30)],
                'required' => true
            ])
            ->add('password', PasswordType::class, [
                'required' => true
            ])
            ->add('isEmployer', CheckboxType::class, [
                'label' => 'Zaznacz jeśli jesteś pracodawcą',
                'required' => false,
            ])
            ->add('save', SubmitType::class);
    }
}
