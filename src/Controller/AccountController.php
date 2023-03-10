<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ApplicationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/profile', name: 'app_user_profile')]
    public function profile(ApplicationRepository $applicationRepository): Response
    {
        $applications = $applicationRepository->findBy(['owner' => $this->getUser()->getId()]);

        return $this->render('user/profile.html.twig', [
            'applications' => $applications
        ]);
    }
}
