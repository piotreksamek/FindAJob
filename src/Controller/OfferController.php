<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Offer;
use App\Enum\Role;
use App\Form\Request\Offer\CreateOfferRequest;
use App\Form\Request\Offer\EditOfferRequest;
use App\Form\Type\CreateOfferFormType;
use App\Form\Type\EditOfferFormType;
use App\Message\AddOfferCommand;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $createOfferRequest = new CreateOfferRequest();

        $form = $this->createForm(CreateOfferFormType::class, $createOfferRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $company = $this->getUser()->getCompany();

            $offer = new Offer(
                $createOfferRequest->name,
                $createOfferRequest->description,
                $createOfferRequest->price,
                $createOfferRequest->city,
                $company
            );

            $entityManager->persist($offer);
            $entityManager->flush();
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

            $offer->update($editOfferRequest->city, $editOfferRequest->price, $editOfferRequest->description);

            $this->em->persist($offer);
            $this->em->flush();

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

        $this->em->remove($offer);
        $this->em->flush();

        return $this->redirectToRoute('app_profile_company_owner');
    }
}
