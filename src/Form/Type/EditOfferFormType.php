<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Request\Offer\EditOfferRequest;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditOfferFormType extends AbstractOfferFormType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EditOfferRequest::class,
        ]);
    }
}
