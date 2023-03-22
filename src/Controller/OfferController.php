<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Offer;
use App\Enum\Role;
use App\Form\Request\Offer\CreateOfferRequest;
use App\Form\Request\Offer\EditOfferRequest;
use App\Form\Type\Offer\CreateOfferFormType;
use App\Form\Type\Offer\EditOfferFormType;
use App\Repository\OfferRepository;
use App\Service\OfferService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{
    public function __construct(private OfferService $offerService)
    {
    }

    #[Route('/offers', name: 'app_offers')]
    public function offers(OfferRepository $offerRepository): Response
    {
        $offers = $offerRepository->findAll();
        return $this->render('offer/offers.html.twig', [
            'offers' => $offers
        ]);
    }

    #[Route('/offers/{slug}', name: 'app_offer_show')]
    public function offer(Offer $offer): Response
    {
        return $this->render('offer/offer.html.twig', [
            'offer' => $offer
        ]);
    }

    #[IsGranted(Role::ROLE_EMPLOYER)]
    #[Route('profile/company/new/offer', name: 'app_new_offer')]
    public function new(Request $request): Response
    {
        $company = $this->getUser()->getCompany();
        $offers = $company->getOffers();

        if (count($offers->toArray()) === 3) {
            $this->addFlash('danger', 'You cannot add more offers');

            return $this->redirectToRoute('app_profile_company_owner');
        }

        $createOfferRequest = new CreateOfferRequest();
        $form = $this->createForm(CreateOfferFormType::class, $createOfferRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->offerService->validateNewOffer($company, $createOfferRequest->name)) {
                $this->addFlash('danger', 'You already have an offer with this name');

                return $this->redirectToRoute('app_profile_company_owner');
            }

            $this->offerService->addNewOffer($createOfferRequest, $company);

            $this->addFlash('success', 'Offer added successfully!');

            return $this->redirectToRoute('app_profile_company_owner');
        }

        return $this->render('offer/new_offer.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/offers/edit/{slug}', name: 'app_edit_offer')]
    public function edit(Offer $offer, Request $request): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $offer);

        $editOfferRequest = new EditOfferRequest($offer->getCity(), $offer->getPrice(), $offer->getDescription());

        $form = $this->createForm(EditOfferFormType::class, $editOfferRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->offerService->updateOffer($offer, $editOfferRequest);

            return $this->redirectToRoute('app_offer_show', ['slug' => $offer->getSlug()]);
        }

        return $this->render('offer/edit.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/offers/delete/{slug}', name: 'app_delete_offer')]
    public function delete(Offer $offer): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $offer);

        $this->offerService->deleteOffer($offer);

        return $this->redirectToRoute('app_profile_company_owner');
    }
}
