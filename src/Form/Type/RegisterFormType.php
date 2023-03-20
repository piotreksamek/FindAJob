<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [new NotBlank()],
                'required' => true
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [new NotBlank()],
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'constraints' => [new NotBlank(), new Email()],
                'required' => true
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('isEmployer', CheckboxType::class, [
                'label' => 'Zaznacz jeśli jesteś pracodawcą',
                'required' => false,
            ])
            ->add('save', SubmitType::class);
    }
}
