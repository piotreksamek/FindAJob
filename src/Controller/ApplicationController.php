<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Offer;
use App\Entity\User;
use App\Enum\Role;
use App\Form\Request\Application\ApplicationRequest;
use App\Form\Type\ApplicationFormType;
use App\Message\AddApplicationCommand;
use App\Repository\ApplicationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationController extends AbstractController
{
    #[Route('/offers/application/{slug}', name: 'app_application')]
    public function application(Offer $offer, Request $request, MessageBusInterface $bus): Response
    {
        $applicationRequest = new ApplicationRequest();

        $form = $this->createForm(ApplicationFormType::class, $applicationRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $bus->dispatch(new AddApplicationCommand($user, $offer, $applicationRequest->description));
        }

        return $this->render('application/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted(Role::ROLE_EMPLOYER)]
    #[Route('/offers/applications/{slug}', name: 'app_application_show_list')]
    public function showList(Offer $offer, ApplicationRepository $applicationRepository): Response
    {
        $applications = $applicationRepository->findBy(['offer' => $offer->getId()]);

        return $this->render('application/show_list.html.twig', [
            'applications' => $applications,
        ]);
    }

    #[Route('/application/{username}/{id}', name: 'app_application_show')]
    public function show(Application $application): Response
    {
        $this->denyAccessUnlessGranted('VIEW', $application);

        return $this->render('application/show.html.twig', [
            'application' => $application
        ]);
    }
}
