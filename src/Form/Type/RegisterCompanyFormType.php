<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Repository\CompanyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegisterCompanyFormType extends AbstractType
{
    public function __construct(private CompanyRepository $companyRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank(), new Callback([$this, 'validateUniqueName'])],
            ])
            ->add('city', TextType::class, [
                'constraints' => [],
                'required' => false,
            ])
            ->add('submit', SubmitType::class);
    }

    public function validateUniqueName($data, ExecutionContextInterface $context)
    {
        $nameExists = $this->checkIfNameExists($data);

        if ($nameExists) {
            $context->buildViolation('Such a company already exists')
                ->atPath('name')
                ->addViolation();
        }
    }

    private function checkIfNameExists($name): bool
    {
        $company = $this->companyRepository->findOneBy(['name' => $name]);
        if($company === null){

            return false;
        }

        return true;
    }
}
