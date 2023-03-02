<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Offer;
use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{
    #[Route('/offers', name: 'app_offers')]
    public function offers(OfferRepository $offerRepository): Response
    {
        $offers = $offerRepository->findAll();
        return $this->render('offers.html.twig', [
            'offers' => $offers
        ]);
    }

    #[Route('/offers/{slug}', name: 'app_offer_show')]
    public function offer(Offer $offer): Response
    {
        return $this->render('offer.html.twig', [
            'offer' => $offer
        ]);
    }
}
