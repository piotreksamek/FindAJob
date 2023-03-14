<?php

declare(strict_types=1);

namespace App\Form\Type\Offer;

use App\Form\Request\Offer\CreateOfferRequest;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class CreateOfferFormType extends AbstractOfferFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [new Length(max: 30)],
                    'label' => 'Offer Name*',
                    'required' => true
                ]
            );

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CreateOfferRequest::class,
        ]);
    }
}
