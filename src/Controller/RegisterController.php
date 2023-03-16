<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\Role;
use App\Form\Request\Company\CreateCompanyRequest;
use App\Form\Request\User\CreateUserRequest;
use App\Form\Type\RegisterCompanyFormType;
use App\Form\Type\RegisterFormType;
use App\Message\CreateCompanyCommand;
use App\Message\CreateUserCommand;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class RegisterController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserAuthenticatorInterface $userAuthenticator,
        private FormLoginAuthenticator $authenticator,
        private MessageBusInterface $bus
    ) {
    }

    #[IsGranted('IS_ANONYMOUS')]
    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $createUserRequest = new CreateUserRequest();

        $form = $this->createForm(RegisterFormType::class, $createUserRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
            $user = $this->bus->dispatch(new CreateUserCommand(
                $createUserRequest->email,
                $createUserRequest->firstName,
                $createUserRequest->lastName,
                $createUserRequest->password,
                $createUserRequest->isEmployer
            ));
            } catch(\Exception $exception){
                $this->addFlash('danger', 'An account with this email address already exists');

                return $this->redirectToRoute('app_register');

            }
            // TODO Do email verification
            $email = $user->getMessage()->getEmail();
            $user = $this->userRepository->findOneBy(['email' => $email]);

            $this->userAuthenticator->authenticateUser($user, $this->authenticator, $request);

            return $this->redirectToRoute('app_user_profile');
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

        $createCompanyRequest = new CreateCompanyRequest();

        $form = $this->createForm(RegisterCompanyFormType::class, $createCompanyRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->bus->dispatch(new CreateCompanyCommand(
                $createCompanyRequest->name,
                $createCompanyRequest->city ?? null,
                $user
            ));
            $this->userAuthenticator->authenticateUser($user, $this->authenticator, $request);

            return $this->redirectToRoute('app_profile_company_owner');

        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
