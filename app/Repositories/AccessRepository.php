<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Access;
use PDO;

class AccessRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(int $ownerUserId, int $grantedUserId): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO accesses (owner_user_id, granted_user_id)
            VALUES (:owner_user_id, :granted_user_id)
        ');

        return $stmt->execute([
            'owner_user_id' => $ownerUserId,
            'granted_user_id' => $grantedUserId,
        ]);
    }

    public function hasAccess(int $ownerUserId, int $grantedUserId): bool
    {
        $stmt = $this->db->prepare('
            SELECT COUNT(*) FROM accesses
            WHERE owner_user_id = :owner_user_id
            AND granted_user_id = :granted_user_id
        ');
        $stmt->execute([
            'owner_user_id' => $ownerUserId,
            'granted_user_id' => $grantedUserId,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function findByOwner(int $ownerUserId): array
    {
        $stmt = $this->db->prepare('
            SELECT * FROM accesses WHERE owner_user_id = :owner_user_id
        ');
        $stmt->execute(['owner_user_id' => $ownerUserId]);

        $accesses = [];
        foreach ($stmt->fetchAll() as $row) {
            $accesses[] = new Access(
                (int) $row['owner_user_id'],
                (int) $row['granted_user_id'],
                (int) $row['id'],
                $row['created_at']
            );
        }
        return $accesses;
    }
}
