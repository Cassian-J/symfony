<?php

namespace App\Security;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->entityManager->getRepository(Users::class)->findOneBy(['Email' => $identifier]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof Users) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        // Reload the user from the database
        if (null === $reloadedUser = $this->entityManager->getRepository(Users::class)->find($user->getUserUuid())) {
            throw new UserNotFoundException();
        }

        return $reloadedUser;
    }

    public function supportsClass(string $class): bool
    {
        return Users::class === $class || is_subclass_of($class, Users::class);
    }
} 