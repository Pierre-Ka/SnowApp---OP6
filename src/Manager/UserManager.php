<?php

namespace App\Manager;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager
{
    private UserRepository $userRepository ;
    private EntityManagerInterface $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    public function create(UserInterface $user): void
    {
        $user->setIsVerified(false);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function edit(UserInterface $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function editPassword(UserInterface $user, string $encodedPassword): void
    {
        $user->setPassword($encodedPassword);
        $user->eraseCredentials();
        $this->em->flush();
    }

    public function verifyUser(Request $request, UserInterface $user): bool
    {
        $now = new \Datetime;
        if ($user->getExpiresToken() < $now) {
            $user->eraseCredentials();
            return false;
        }
        $user->setIsVerified(true);
        $user->eraseCredentials();
        $this->em->flush();
        return true;
    }

    public function verifyUserForResetPassword(UserInterface $user): bool
    {
        $now = new \Datetime;
        if ($user->getExpiresToken() < $now) {
            $user->eraseCredentials();
            return false;
        }
        return true;
    }

    public function defineTokenRelatedProperties(UserInterface $user, string $token): void
    {
        $user->setToken($token);
        $user->setExpiresToken(new \DateTime('+1 week'));
        $this->em->flush();
    }

    public function defineProfilePicture($formData, UserInterface $user)
    {
        $extension = $formData->guessExtension();
        if ($user->getProfilePicture())
        {
            $formData->move('../public/uploads/user/', $user->getProfilePicture());
        }
        else
        {
            $nameUserWithoutSpace = str_replace(" ", "", $user->getFullName());
            $nameUserLower = strtolower($nameUserWithoutSpace);
            $setFileName = $nameUserLower.'_USER_'.rand(1, 999).'.'.$extension ;
            $user->setProfilePicture($setFileName);
            $formData->move('../public/uploads/user/', $user->getProfilePicture());
        }
    }

}