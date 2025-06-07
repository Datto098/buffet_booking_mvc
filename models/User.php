<?php
/**
 * User Model
 */

require_once 'BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';

    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findByRole($role) {
        $sql = "SELECT * FROM {$this->table} WHERE role = :role";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':role', $role);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByCondition($condition) {
        $where = [];
        foreach ($condition as $key => $value) {
            $where[] = "$key = :$key";
        }
        $whereClause = implode(' AND ', $where);

        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        $stmt = $this->db->prepare($sql);

        foreach ($condition as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createUser($data) {
        if (isset($data['full_name'])) {
            $parts = explode(' ', trim($data['full_name']));
            $data['first_name'] = array_shift($parts);
            $data['last_name'] = implode(' ', $parts);
            unset($data['full_name']);
        }
        if (isset($data['phone_number'])) {
            $data['phone'] = $data['phone_number'];
            unset($data['phone_number']);
        }
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->create($data);
    }

    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    public function getUserAddresses($userId) {
        $sql = "SELECT * FROM user_addresses WHERE user_id = :user_id ORDER BY is_default DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function addAddress($userId, $addressData) {
        $addressData['user_id'] = $userId;

        $sql = "INSERT INTO user_addresses (user_id, address_line, is_default) VALUES (:user_id, :address_line, :is_default)";
        $stmt = $this->db->prepare($sql);

        foreach ($addressData as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    public function updateAddress($addressId, $addressData) {
        $sql = "UPDATE user_addresses SET address_line = :address_line, is_default = :is_default WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':address_line', $addressData['address_line']);
        $stmt->bindValue(':is_default', $addressData['is_default']);
        $stmt->bindValue(':id', $addressId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteAddress($addressId) {
        $sql = "DELETE FROM user_addresses WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $addressId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Get favorite foods for a user
     * @param int $userId User ID
     * @return array Array of favorite foods
     */
    public function getFavoriteFoods($userId) {
        $sql = "SELECT f.*, uf.created_at as favorited_at
                FROM user_favorites uf
                JOIN foods f ON uf.food_id = f.id
                WHERE uf.user_id = :user_id
                ORDER BY uf.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Add a food to user's favorites
     * @param int $userId User ID
     * @param int $foodId Food ID
     * @return bool Success status
     */
    public function addFavoriteFood($userId, $foodId) {
        // Check if already exists
        $sql = "SELECT COUNT(*) FROM user_favorites WHERE user_id = :user_id AND food_id = :food_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':food_id', $foodId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->fetchColumn() > 0) {
            return true; // Already exists
        }

        // Add to favorites
        $sql = "INSERT INTO user_favorites (user_id, food_id) VALUES (:user_id, :food_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':food_id', $foodId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Remove a food from user's favorites
     * @param int $userId User ID
     * @param int $foodId Food ID
     * @return bool Success status
     */
    public function removeFavoriteFood($userId, $foodId) {
        $sql = "DELETE FROM user_favorites WHERE user_id = :user_id AND food_id = :food_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':food_id', $foodId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Update user avatar
     * @param int $userId User ID
     * @param string $avatarPath Avatar file path
     * @return bool Success status
     */
    public function updateUserAvatar($userId, $avatarPath) {
        $sql = "UPDATE {$this->table} SET avatar = :avatar WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':avatar', $avatarPath);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Update user information
     * @param int $userId User ID
     * @param array $updateData Data to update
     * @return bool Success status
     */
    public function updateUser($userId, $updateData) {
        $fields = [];
        $params = [':id' => $userId];

        foreach ($updateData as $field => $value) {
            $fields[] = "$field = :$field";
            $params[":$field"] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    /**
     * Update user password
     * @param int $userId User ID
     * @param string $newPassword New password (will be hashed)
     * @return bool Success status
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE {$this->table} SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getUsersByRole($role) {
        $sql = "SELECT * FROM {$this->table} WHERE role = :role ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':role', $role);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
