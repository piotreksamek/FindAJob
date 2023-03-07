<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Offer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class OfferFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [new Length(max: 50)],
                    'label' => 'Offer Name*',
                    'required' => true
                ]
            )
            ->add('city', TextType::class,[
                'constraints' => [new Length(max: 50)],
                'required' => false,
            ])
            ->add('price', TextType::class, [
                'constraints' => [new Length(max: 10)],
                'label' => 'Salary*',
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [new Length(max: 50)],
                'label' => 'Description*',
                'required' => true
            ])
            ->add('submit', SubmitType::class);

//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//            $product = $event->getData();
//            $form = $event->getForm();
//
//            if (!$product || null === $product->getId()) {
//                $form->add('name', TextType::class);
//            }
//        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
}
