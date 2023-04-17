<?php

declare(strict_types=1);

namespace App\Api\Controller;

use App\Api\Serializer\OfferSerializer;
use App\Api\Service\ApiOfferService;
use App\Entity\Offer;
use App\Form\Request\Offer\EditOfferRequest;
use App\Repository\OfferRepository;
use App\Service\OfferService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{
    public function __construct(
        private OfferRepository $offerRepository,
        private OfferSerializer $serializer,
        private ApiOfferService $apiOfferService,
        private OfferService $offerService
    ) {
    }

    #[Route('/api/offers', methods: ['GET'])]
    public function offers(): Response
    {
        $offers = $this->offerRepository->findAll();

        return new JsonResponse($this->serializer->serializeOffers($offers));
    }

    #[Route('/api/offers/{id}', methods: ['GET'])]
    public function offer(int $id): Response
    {
        $offer = $this->offerRepository->find($id);

        return new JsonResponse($this->serializer->serializeOffer($offer));
    }

    #[Route('/api/offers', methods: ['POST'])]
    public function addOffer(Request $request): Response
    {
        $companyId = $this->getUser()->getCompany()->getId();

        if (!$this->offerService->canAddOffer($companyId)) {

            return new JsonResponse('Wykorzystałeś limit ofert');
        }

        $data = json_decode($request->getContent(), true);

        $this->apiOfferService->addOffer($data, $companyId);

        return new JsonResponse('Success');
    }

    #[Route('/api/offers/{id}', methods: ['PUT'])]
    public function editOffer(int $id, OfferRepository $offerRepository, Request $request, Offer $offer): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $offer);

        $data = json_decode($request->getContent(), true);

        $offer = $offerRepository->findOneBy(['id' => $id]);
        $editOfferRequest = new EditOfferRequest($data['city'], $data['price'], $data['description']);
        $this->offerService->updateOffer($offer, $editOfferRequest);

        return new JsonResponse('Success');
    }

    #[Route('/api/offers/{id}', methods: ['DELETE'])]
    public function deleteOffer(int $id, Offer $offer): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $offer);

        $offer = $this->offerRepository->findOneBy(['id' => $id]);

        $this->offerService->deleteOffer($offer);

        return new JsonResponse('Delete Offer');
    }
}
