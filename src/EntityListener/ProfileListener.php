<?php

namespace App\EntityListener;

use App\EntityExtra\Profile;
use App\Repository\UserRepository;

class ProfileListener
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function postLoad(Profile $profile): void
    {
        $user = $this->userRepository->findOneBy(['email' => $profile->getEmail()]);

        if ($user) {
            $profile->setUser($user);
        }
    }
}
