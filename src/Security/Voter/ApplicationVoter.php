<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Application;
use App\Entity\Offer;
use App\Entity\User;
use App\Repository\ApplicationRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ApplicationVoter extends Voter
{
    private const VIEW = 'VIEW';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::VIEW])) {
            return false;
        }

        if (!$subject instanceof Application) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canEdit($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Application $application, User $user): bool
    {
        return $user->getId() === $application->getOwner()->getId()
            || $user->getCompany() === $application->getOffer()->getOwner();
    }
}
