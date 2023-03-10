<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Company;
use App\Enum\Role;
use App\Form\Request\Employer\AddEmployerRequest;
use App\Form\Type\AddEmployerFormType;
use App\Message\AddEmployerCommand;
use App\Repository\CompanyRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    #[IsGranted(Role::ROLE_EMPLOYER)]
    #[Route('/profile/company', name: 'app_profile_company_owner')]
    public function show(CompanyRepository $companyRepository, Request $request, MessageBusInterface $bus): Response
    {
        /** @var Company $company */
        $company = $companyRepository->findOneBy(['id' => $this->getUser()->getCompany()]);
        $companyEmployers = $company->getUsers()->toArray();
        $offers = $company->getOffers();

        $addEmployeeRequest = new AddEmployerRequest();
        $form = $this->createForm(AddEmployerFormType::class, $addEmployeeRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $bus->dispatch(new AddEmployerCommand($addEmployeeRequest->email, $company));
            } catch (\Exception $exception) {
                $this->addFlash('danger', $exception->getPrevious()->getMessage());
            }
        }

        return $this->render('company/profile.html.twig', [
            'company' => $company,
            'offers' => $offers,
            'form' => $form->createView(),
            'employers' => $companyEmployers,
        ]);
    }
}
