<?php

namespace App\Services;

use App\Repositories\AccessRepository;
use App\Repositories\UserRepository;

class AccessService
{
    private AccessRepository $accessRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->accessRepository = new AccessRepository();
        $this->userRepository = new UserRepository();
    }

    public function grantAccess(int $ownerUserId, int $grantedUserId): array
    {
        $user = $this->userRepository->findById($grantedUserId);
        if (!$user) {
            return ['error' => 'User not found'];
        }

        if ($ownerUserId === $grantedUserId) {
            return ['error' => 'Cannot give access to yourself'];
        }

        if ($this->accessRepository->hasAccess($ownerUserId, $grantedUserId)) {
            return ['error' => 'Access already granted'];
        }

        $saved = $this->accessRepository->create($ownerUserId, $grantedUserId);
        if (!$saved) {
            return ['error' => 'Failed to grant access'];
        }

        return ['message' => 'Access granted successfully'];
    }

    public function getUsersWithAccess(int $ownerUserId): array
    {
        $accesses = $this->accessRepository->findByOwner($ownerUserId);
        $users = [];

        foreach ($accesses as $access) {
            $user = $this->userRepository->findById($access->getGrantedUserId());
            if ($user) {
                $users[] = [
                    'id' => $user->getId(),
                    'login' => $user->getLogin(),
                ];
            }
        }

        return $users;
    }
}
