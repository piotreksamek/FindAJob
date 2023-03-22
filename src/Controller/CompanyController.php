<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\Role;
use App\Form\Request\Employer\AddEmployerRequest;
use App\Form\Type\AddEmployerFormType;
use App\Message\AddEmployerCommand;
use App\Repository\CompanyRepository;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private MessageBusInterface $bus,
    ) {
    }

    #[IsGranted(Role::ROLE_EMPLOYER)]
    #[Route('/profile/company', name: 'app_profile_company_owner')]
    public function show(Request $request, UserService $userNotificationSender): Response
    {
        $company = $this->getUser()->getCompany();

        if ($company === null) {
            return $this->redirectToRoute('app_register_company');
        }

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

            $userNotificationSender->sendNotification($company, $addEmployeeRequest);

            $this->addFlash('success', 'Success!');

            return $this->redirectToRoute('app_profile_company_owner');
        }

        return $this->render('company/profile.html.twig', [
            'company' => $company,
            'offers' => $company->getOffers(),
            'form' => $form->createView(),
            'employers' => $this->companyRepository->findCompanyByCompanyId($company),
        ]);
    }
}
