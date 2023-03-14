<?php

declare(strict_types=1);

namespace App\Form\Type\Offer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

abstract class AbstractOfferFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', TextType::class,[
                'constraints' => [new Length(max: 30)],
                'required' => false,
            ])
            ->add('price', TextType::class, [
                'constraints' => [new Length(max: 10)],
                'label' => 'Salary*',
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description*',
                'required' => true
            ])
            ->add('submit', SubmitType::class);
    }
}
