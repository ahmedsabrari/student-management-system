<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\AuthService;
use App\Helpers\Session;
use App\Models\User;

/**
 * AuthController
 *
 * Handles user authentication processes like login, registration, and logout.
 * It acts as a bridge between user requests and the authentication business logic.
 */
class AuthController extends Controller
{
    /**
     * @var AuthService Handles the core authentication logic.
     */
    protected $authService;

    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        // In a real application with Dependency Injection, this would be injected.
        // For now, we instantiate it directly.
        $this->authService = new AuthService();
    }

    /**
     * Displays the login form.
     * Handles GET requests to /login.
     *
     * @return string The rendered login view.
     */
    public function showLoginForm(): string
    {
        // We will pass an 'auth' layout to the render method
        return $this->render('auth/login', [], 'auth');
    }

    /**
     * Processes the login attempt.
     * Handles POST requests to /login.
     */
    public function login(): void
    {
        $request = new Request();
        $email = $request->input('email');
        $password = $request->input('password');

        // Basic validation
        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email and password are required.');
            $this->redirect('/login');
        }

        $user = $this->authService->attemptLogin($email, $password);

        if ($user) {
            Session::flash('success', 'Welcome back!');
            $this->redirect('/dashboard');
        } else {
            Session::flash('error', 'Invalid credentials. Please try again.');
            $this->redirect('/login');
        }
    }

    /**
     * Displays the registration form.
     * Handles GET requests to /register.
     *
     * @return string The rendered registration view.
     */
    public function showRegistrationForm(): string
    {
        return $this->render('auth/register', [], 'auth');
    }

    /**
     * Processes a new user registration.
     * Handles POST requests to /register.
     */
    public function register(): void
    {
        $request = new Request();
        $data = [
            'full_name' => $request->input('full_name'),
            'username'  => $request->input('username'),
            'email'     => $request->input('email'),
            'password'  => $request->input('password'),
            'password_confirm' => $request->input('password_confirm'),
        ];

        // @TODO: Implement a robust Validator class
        if ($data['password'] !== $data['password_confirm']) {
            Session::flash('error', 'Passwords do not match.');
            $this->redirect('/register');
        }
        
        // Remove confirmation password before passing to service
        unset($data['password_confirm']);

        $result = $this->authService->registerUser($data);

        if ($result) {
            Session::flash('success', 'Registration successful! Please log in.');
            $this->redirect('/login');
        } else {
            // The service should ideally set a more specific error message
            Session::flash('error', 'Registration failed. The username or email may already be taken.');
            $this->redirect('/register');
        }
    }

    /**
     * Logs the user out.
     * Handles POST requests to /logout.
     */
    public function logout(): void
    {
        $this->authService->logout();
        Session::flash('success', 'You have been successfully logged out.');
        $this->redirect('/login');
    }
}