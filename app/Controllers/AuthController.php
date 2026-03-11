<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Auth;
use App\Core\CSRF;
use App\Models\User;

class AuthController extends Controller
{
    public function loginForm(Request $request): void
    {
        if (Auth::check()) $this->redirect('/');
        $this->render('auth/login', ['pageTitle' => 'Login — TENIKO'], 'auth');
    }

    public function login(Request $request): void
    {
        $this->verifyCsrf($request);
        if (Auth::check()) $this->redirect('/');

        $email    = trim($request->post('email', ''));
        $password = $request->post('password', '');
        $remember = $request->post('remember', false);

        if (!$email || !$password) {
            $this->session->flash('error', 'Please fill in all fields.');
            $this->redirect('/login');
        }

        if (Auth::attempt($email, $password)) {
            $intended = $this->session->get('intended_url', '/');
            $this->session->remove('intended_url');
            $this->session->flash('success', 'Welcome back!');
            $this->redirect($intended);
        } else {
            $this->session->flash('error', 'Invalid email or password. Please try again.');
            $this->redirect('/login');
        }
    }

    public function registerForm(Request $request): void
    {
        if (Auth::check()) $this->redirect('/');
        $this->render('auth/register', ['pageTitle' => 'Join TENIKO — Register'], 'auth');
    }

    public function register(Request $request): void
    {
        $this->verifyCsrf($request);
        if (Auth::check()) $this->redirect('/');

        $errors = $request->validate([
            'username' => 'required|min:3|max:60',
            'email'    => 'required|email|max:191',
            'password' => 'required|min:8|max:255',
        ]);

        if (!empty($errors)) {
            $this->session->flash('error', implode(' ', array_merge(...array_values($errors))));
            $this->redirect('/register');
        }

        $userModel = new User();
        $username = trim($request->post('username'));
        $email    = strtolower(trim($request->post('email')));

        if ($userModel->findByEmail($email)) {
            $this->session->flash('error', 'This email is already registered.');
            $this->redirect('/register');
        }
        if ($userModel->findByUsername($username)) {
            $this->session->flash('error', 'This username is already taken.');
            $this->redirect('/register');
        }

        $userId = $userModel->register([
            'username'     => $username,
            'email'        => $email,
            'password'     => $request->post('password'),
            'display_name' => $username,
        ]);

        // TODO: send verification email
        $this->session->flash('success', 'Account created! Please check your email to verify your account.');
        $this->redirect('/login');
    }

    public function logout(Request $request): void
    {
        Auth::logout();
        $this->session->flash('info', 'You have been logged out.');
        $this->redirect('/');
    }

    public function verifyEmail(Request $request): void
    {
        $token = $request->get('token', '');
        $userModel = new User();
        if ($userModel->verify($token)) {
            $this->session->flash('success', 'Your email has been verified. You can now log in.');
        } else {
            $this->session->flash('error', 'Invalid or expired verification link.');
        }
        $this->redirect('/login');
    }

    public function forgotForm(Request $request): void
    {
        $this->render('auth/forgot', ['pageTitle' => 'Forgot Password — TENIKO'], 'auth');
    }

    public function forgotPassword(Request $request): void
    {
        $this->verifyCsrf($request);
        $email = strtolower(trim($request->post('email', '')));
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        // Always show success to prevent email enumeration
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $userModel->update($user['id'], [
                'password_reset_token'   => $token,
                'password_reset_expires' => date('Y-m-d H:i:s', time() + 3600),
            ]);
            // TODO: send password reset email
        }
        $this->session->flash('success', 'If that email is registered, a reset link has been sent.');
        $this->redirect('/forgot-password');
    }

    public function resetForm(Request $request): void
    {
        $this->render('auth/reset', ['token' => $request->get('token'), 'pageTitle' => 'Reset Password — TENIKO'], 'auth');
    }

    public function resetPassword(Request $request): void
    {
        $this->verifyCsrf($request);
        $token    = $request->post('token', '');
        $password = $request->post('password', '');

        if (strlen($password) < 8) {
            $this->session->flash('error', 'Password must be at least 8 characters.');
            $this->redirect('/reset-password?token=' . urlencode($token));
        }

        $user = $this->db()->fetch(
            "SELECT * FROM users WHERE password_reset_token=? AND password_reset_expires > NOW()", [$token]
        );
        if (!$user) {
            $this->session->flash('error', 'Invalid or expired reset link.');
            $this->redirect('/forgot-password');
        }

        $userModel = new User();
        $userModel->update($user['id'], [
            'password'               => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
            'password_reset_token'   => null,
            'password_reset_expires' => null,
        ]);
        $this->session->flash('success', 'Password updated successfully. Please log in.');
        $this->redirect('/login');
    }
}
