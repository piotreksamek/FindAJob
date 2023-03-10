<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Company;
use App\Entity\Offer;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OfferVoter extends Voter
{
    const EDIT = 'EDIT';

    const DELETE = 'DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Offer) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if(!$user){
            return false;
        }

        $company = $user->getCompany();

        if (!$company instanceof Company) {

            return false;
        }

        if (!$subject instanceof Offer) {
            throw new \Exception('Wront type somehow passed');
        }

        /** @var Offer $post */
        $post = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($post, $company);

            case self::DELETE:
                return $this->canDelete($post, $company);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Offer $offer, Company $company): bool
    {
        return $company->getId() === $offer->getOwner()->getId();
    }

    private function canDelete(Offer $offer, Company $company): bool
    {
        return $company->getId() === $offer->getOwner()->getId();
    }
}
