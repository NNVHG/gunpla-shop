<?php
declare(strict_types=1);

class User
{
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT id, full_name, email, phone, address, role, created_at FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): int|false
    {
        if ($this->findByEmail($data['email'])) return false;
        $stmt = $this->db->prepare("INSERT INTO users (full_name, email, password, phone, address, role) VALUES (:full_name, :email, :password, :phone, :address, :role)");
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':email'     => $data['email'],
            ':password'  => password_hash($data['password'], PASSWORD_BCRYPT),
            ':phone'     => $data['phone']   ?? null,
            ':address'   => $data['address'] ?? null,
            ':role'      => $data['role']    ?? 'customer',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = []; $params = [':id' => $id];
        foreach (['full_name','phone','address'] as $f) {
            if (isset($data[$f])) { $fields[] = "$f = :$f"; $params[":$f"] = $data[$f]; }
        }
        if (empty($fields)) return false;
        return $this->db->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id")->execute($params);
    }

    public function authenticate(string $email, string $password): array|false
    {
        $user = $this->findByEmail($email);
        if (!$user) return false;
        if (!password_verify($password, $user['password'])) return false;
        return $user;
    }

    // ─── ĐĂNG KÝ ────────────────────────────────────────────────────

    /**
     * Validate dữ liệu form đăng ký
     * Trả về mảng lỗi — rỗng nghĩa là hợp lệ
     */
    public function validateRegister(array $post): array
    {
        $errors = [];

        if (empty(trim($post['full_name'] ?? '')))
            $errors['full_name'] = 'Vui lòng nhập họ tên';

        if (empty(trim($post['email'] ?? '')))
            $errors['email'] = 'Vui lòng nhập email';
        elseif (!filter_var(trim($post['email']), FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'Email không hợp lệ';
        elseif ($this->findByEmail(trim($post['email'])))
            $errors['email'] = 'Email này đã được đăng ký';

        if (empty($post['password'] ?? ''))
            $errors['password'] = 'Vui lòng nhập mật khẩu';
        elseif (strlen($post['password']) < 8)
            $errors['password'] = 'Mật khẩu tối thiểu 8 ký tự';

        if (($post['password'] ?? '') !== ($post['password_confirm'] ?? ''))
            $errors['password_confirm'] = 'Mật khẩu xác nhận không khớp';

        if (!empty($post['phone']) && !preg_match('/^(0|\+84)[0-9]{9,10}$/', trim($post['phone'])))
            $errors['phone'] = 'Số điện thoại không hợp lệ';

        return $errors;
    }

    /**
     * Tạo tài khoản mới từ dữ liệu form
     * Trả về ['success'=>bool, 'user_id'=>int, 'message'=>string]
     */
    public function register(array $post): array
    {
        $email = trim($post['email'] ?? '');

        if ($this->findByEmail($email)) {
            return ['success' => false, 'message' => 'Email này đã được đăng ký'];
        }

        $stmt = $this->db->prepare("
            INSERT INTO users (full_name, email, password, phone, address, role)
            VALUES (:full_name, :email, :password, :phone, :address, 'customer')
        ");
        $stmt->execute([
            ':full_name' => trim($post['full_name']),
            ':email'     => $email,
            ':password'  => password_hash($post['password'], PASSWORD_BCRYPT),
            ':phone'     => trim($post['phone']   ?? '') ?: null,
            ':address'   => trim($post['address'] ?? '') ?: null,
        ]);

        return [
            'success' => true,
            'user_id' => (int) $this->db->lastInsertId(),
            'message' => 'Đăng ký thành công',
        ];
    }

    // ─── CẬP NHẬT PROFILE ───────────────────────────────────────────

    /**
     * Cập nhật thông tin cá nhân (không đổi mật khẩu ở đây)
     */
    public function updateProfile(int $id, array $post): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET full_name = :full_name,
                phone     = :phone,
                address   = :address
            WHERE id = :id
        ");
        return $stmt->execute([
            ':full_name' => trim($post['full_name'] ?? ''),
            ':phone'     => trim($post['phone']     ?? '') ?: null,
            ':address'   => trim($post['address']   ?? '') ?: null,
            ':id'        => $id,
        ]);
    }

    /**
     * Đổi mật khẩu — chỉ cần new password (Admin reset hoặc đã xác thực trước)
     * UserController đã kiểm tra độ dài trước khi gọi hàm này
     */
    public function changePassword(int $id, string $newPassword): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET password = :p WHERE id = :id");
        return $stmt->execute([
            ':p'  => password_hash($newPassword, PASSWORD_BCRYPT),
            ':id' => $id,
        ]);
    }
}