<?php

declare(strict_types=1);

namespace App\Form\Type\Offer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

abstract class AbstractOfferFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', TextType::class,[
                'required' => false,
            ])
            ->add('price', TextType::class, [
                'label' => 'Salary',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description*',
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('submit', SubmitType::class);
    }
}
