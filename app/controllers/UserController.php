<?php
declare(strict_types=1);

class UserController
{
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $this->loginSubmit();
        else $this->loginForm();
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $this->registerSubmit();
        else $this->registerForm();
    }

    public function loginForm(): void {
        if ($this->isLoggedIn()) { $this->redirect('/'); return; }
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);
        $this->render('user/login', ['title' => 'Đăng nhập', 'error' => $error], false);
    }

    public function loginSubmit(): void {
        $this->requirePost();
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');
        $redirect = $_POST['redirect'] ?? '/';

        $user = $this->userModel->authenticate($email, $password);

        if ($user) {
            $_SESSION['user'] = [
                'id'    => $user['id'],
                'name'  => $user['full_name'],
                'email' => $user['email'],
                'role'  => $user['role'],
            ];
            $this->redirect($user['role'] === 'admin' ? '/admin' : $redirect);
        } else {
            $_SESSION['login_error'] = 'Email hoặc mật khẩu không đúng';
            $this->redirect('/user/login');
        }
    }

    public function registerForm(): void {
        if ($this->isLoggedIn()) { $this->redirect('/'); return; }
        $errors = $_SESSION['register_errors'] ?? [];
        $old    = $_SESSION['register_form']   ?? [];
        unset($_SESSION['register_errors'], $_SESSION['register_form']);
        $this->render('user/register', compact('errors', 'old') + ['title' => 'Đăng ký tài khoản'], false);
    }

    public function registerSubmit(): void {
        $this->requirePost();
        $errors = $this->userModel->validateRegister($_POST);

        if (!empty($errors)) {
            $_SESSION['register_errors'] = $errors;
            $_SESSION['register_form']   = array_diff_key($_POST, ['password' => 1, 'password_confirm' => 1]);
            $this->redirect('/user/register');
            return;
        }

        $result = $this->userModel->register($_POST);
        if (!$result['success']) {
            $_SESSION['register_errors'] = ['email' => $result['message']];
            $this->redirect('/user/register');
            return;
        }

        $user = $this->userModel->findById($result['user_id']);
        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['full_name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Chào mừng ' . $user['full_name'] . '!'];
        $this->redirect('/');
    }

    public function logout(): void {
        unset($_SESSION['user']);
        $this->redirect('/');
    }

    public function profile(): void {
        $this->requireLogin();
        $userId = (int) $_SESSION['user']['id'];
        
        $user = $this->userModel->findById($userId);
        
        $orderModel = new Order();
        $orders = $orderModel->getByUser($userId); 
        
        $favoriteModel = new Favorite(getDB());
        $favorites = $favoriteModel->getUserFavorites($userId);

        // Đã sửa 'user/profile' thành 'profile/profile' để khớp với thư mục của bạn
        $this->render('profile/profile', [
            'title'     => 'Trung tâm điều khiển Pilot — GUNPLA SHOP',
            'user'      => $user,
            'orders'    => $orders,
            'favorites' => $favorites,
            'errors'    => $_SESSION['profile_errors'] ?? []
        ]);
        unset($_SESSION['profile_errors']);
    }

    public function profileUpdate(): void {
        $this->requireLogin();
        $this->requirePost();
        $userId = (int) $_SESSION['user']['id'];

        $this->userModel->updateProfile($userId, $_POST);

        if (!empty($_POST['new_password'])) {
            if (strlen($_POST['new_password']) < 8) {
                $_SESSION['profile_errors'] = ['new_password' => 'Mật khẩu tối thiểu 8 ký tự'];
                $this->redirect('/user/profile');
                return;
            }
            $this->userModel->changePassword($userId, $_POST['new_password']);
        }

        $_SESSION['user']['name'] = trim($_POST['full_name']);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Đã cập nhật thông tin tài khoản'];
        $this->redirect('/user/profile');
    }

    private function isLoggedIn(): bool   { return !empty($_SESSION['user']); }
    private function requireLogin(): void { if (!$this->isLoggedIn()) $this->redirect('/user/login'); }
    private function requirePost(): void  { if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; } }
    private function redirect(string $url): void { header('Location: ' . BASE_URL . '/' . ltrim($url, '/')); exit; }
    private function render(string $view, array $data = [], bool $withLayout = true): void {
        extract($data);
        ob_start();
        $f = APP_PATH . '/views/' . $view . '.php';
        if (file_exists($f)) include $f; else echo "<p>View not found: $f</p>";
        $content = ob_get_clean();
        
        if ($withLayout) include APP_PATH . '/views/layouts/main.php';
        else echo $content;
    }
}