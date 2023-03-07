<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    #[Route('/profile/company', name: 'app_profile_company_owner')]
    public function show(CompanyRepository $companyRepository): Response
    {
        /** @var Company $company */
        $company = $companyRepository->findOneBy(['id' => $this->getUser()->getCompany()]);
        $offers = $company->getOffers();

        return $this->render('company/profile.html.twig', [
            'company' => $company,
            'offers' => $offers
        ]);
    }

//    public function addEmployee():Response
//    {
//
//    }
}
