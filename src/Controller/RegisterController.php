<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\Role;
use App\Form\Type\RegisterCompanyFormType;
use App\Form\Type\RegisterFormType;
use App\Service\RegisterFormProcessing;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class RegisterController extends AbstractController
{
    public function __construct(
        private RegisterFormProcessing $processing,
        private UserAuthenticatorInterface $userAuthenticator,
        private FormLoginAuthenticator $authenticator
    ) {
    }

    #[IsGranted('IS_ANONYMOUS')]
    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $form = $this->createForm(RegisterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $user = $this->processing->processingForm($data);

                $this->userAuthenticator->authenticateUser(
                    $user,
                    $this->authenticator,
                    $request
                );
            } catch (\Exception $exception) {
                $this->addFlash('danger', 'An account with this email address already exists');
            }


            return $this->redirectToRoute('app_index');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted(Role::ROLE_EMPLOYER)]
    #[Route('/register/company', name: 'app_register_company')]
    public function registerCompany(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(RegisterCompanyFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->processing->processingCompanyForm($data, $user);

            $this->userAuthenticator->authenticateUser($user, $this->authenticator, $request);

            return $this->redirectToRoute('app_profile_company_owner');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
