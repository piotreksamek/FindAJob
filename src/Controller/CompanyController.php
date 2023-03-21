<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Company;
use App\Enum\Role;
use App\Event\UserNotificationSentEvent;
use App\Form\Request\Employer\AddEmployerRequest;
use App\Form\Type\AddEmployerFormType;
use App\Message\AddEmployerCommand;
use App\Repository\CompanyRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private MessageBusInterface $bus,
        private EventDispatcherInterface $eventDispatcher
    )
    {
    }

    #[IsGranted(Role::ROLE_EMPLOYER)]
    #[Route('/profile/company', name: 'app_profile_company_owner')]
    public function show(Request $request): Response
    {
        if ($this->getUser()->getCompany() === null) {
            return $this->redirectToRoute('app_register_company');
        }

        /** @var Company $company */
        $company = $this->companyRepository->findOneBy(['id' => $this->getUser()->getCompany()]);
        $companyEmployers = $company->getUsers()->toArray();
        $offers = $company->getOffers();

        $addEmployeeRequest = new AddEmployerRequest();
        $form = $this->createForm(AddEmployerFormType::class, $addEmployeeRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->bus->dispatch(new AddEmployerCommand($addEmployeeRequest->email, $company));
            } catch (\Exception $exception) {
                $this->addFlash('danger', $exception->getPrevious()->getMessage());
                return $this->redirectToRoute('app_profile_company_owner');
            }

            $message = new UserNotificationSentEvent(
                $company,
                $addEmployeeRequest->email,
                sprintf('You have been added to a business account %s', $company->getName()
                ));
            $this->eventDispatcher->dispatch($message);

            $this->addFlash('success', 'Success!');

            return $this->redirectToRoute('app_profile_company_owner');
        }

        return $this->render('company/profile.html.twig', [
            'company' => $company,
            'offers' => $offers,
            'form' => $form->createView(),
            'employers' => $companyEmployers,
        ]);
    }
}
