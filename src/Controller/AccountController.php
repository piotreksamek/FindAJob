<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ApplicationRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    public function __construct(private MessageRepository $messageRepository, private  EntityManagerInterface $em)
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/profile', name: 'app_user_profile')]
    public function profile(ApplicationRepository $applicationRepository, ): Response
    {
        $this->showMessages();
        $applications = $applicationRepository->findBy(['owner' => $this->getUser()->getId()]);

        return $this->render('user/profile.html.twig', [
            'applications' => $applications,
        ]);
    }

    private function showMessages(): void
    {
        $messages = $this->messageRepository->findBy(['recipient' => $this->getUser()->getId()]);

        if(!$messages){
            return;
        }

        foreach ($messages as $message) {
            $description = $message->getMessage();
        }

            $this->addFlash('success', $description);
            $this->em->remove($message);
            $this->em->flush();
    }
}
