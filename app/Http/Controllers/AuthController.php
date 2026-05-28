<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Foundation\Application;
class AuthController extends BaseController
{
  public function showLogin(){
    $this->render('Login',['blogOwner' => null],'auth');
  }
  public function showRegister(){

    $this->render('Register',['blogOwner' => null],'auth');
  }
  public function Register(){

    $userModel = new User(Application::$app->db);
    $email = $_POST['email'] ?? '';
    $username = strtolower(trim($_POST['username'] ?? ''));
    $name = $_POST['name'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone = $_POST['phone'] ?? '';

    if (empty($username) || empty($email) || empty($name) || empty($password) || empty($phone)) {
      Application::$app->session->setFlash('error', 'All fields are required');
      $this->redirect('/register');
      return;
    }

    if (!User::validateName($name)) {
      Application::$app->session->setFlash('error', 'Name must contain only letters and spaces, and be at least 3 characters.');
      $this->redirect('/register');
      return;
    }

    if (!User::validateEmail($email)) {
      Application::$app->session->setFlash('error', 'Invalid email format');
      $this->redirect('/register');
      return;
    }

    if (!User::validatePassword($password)) {
      Application::$app->session->setFlash('error', 'Password must be at least 8 characters long');
      $this->redirect('/register');
      return;
    }

    if (!User::validatePhone($phone)) {
      Application::$app->session->setFlash('error', 'Invalid phone number. Must start with +977 and be a valid NTC or Ncell number.');
      $this->redirect('/register');
      return;
    }

    $existingEmail = $userModel->findByEmail($email);
    if($existingEmail){
      Application::$app->session->setFlash('error', 'Email already exists');
      $this->redirect('/register');
      return;
    }

    $existingUsername = $userModel->findByUsername($username);
    if ($existingUsername) {
      Application::$app->session->setFlash('error', 'Username already exists');
      $this->redirect('/register');
      return;
    }

    $userId = $userModel->create($name, $username, $email, $password, $phone);

    if ($userId) {
        $otpService = new \App\Services\OtpService();
        $smsService = new \App\Services\SmsService();
        $otp = $otpService->generateOtp($userId);
        $smsService->sendSms($phone, "Your Blogify OTP is: $otp. It expires in 10 minutes.");

        Application::$app->session->set('pending_verification_user_id', $userId);
        Application::$app->session->setFlash('success', 'Registration successful. Please verify your phone number.');
        $this->redirect('/verify-otp');
    } else {
        Application::$app->session->setFlash('error', 'Registration failed. Please try again.');
        $this->redirect('/register');
    }

  }
  public function Login(){
    $userModel = new User(Application::$app->db);
    $user = $userModel->findByEmail($_POST['email']);
    if(!$user || !password_verify($_POST['password'],$user['password'])){
      Application::$app->session->setFlash('error', 'Invalid credentials');
      $this->redirect('/login');
      return;
    }
    if (!$user['is_verified']) {
        Application::$app->session->set('pending_verification_user_id', $user['id']);
        
        $otpService = new \App\Services\OtpService();
        $smsService = new \App\Services\SmsService();
        $otp = $otpService->generateOtp($user['id']);
        $smsService->sendSms($user['phone'], "Your Blogify OTP is: $otp. It expires in 10 minutes.");
        
        Application::$app->session->setFlash('info', 'Please verify your phone number to continue.');
        $this->redirect('/verify-otp');
        return;
    }

    Application::$app->session->set('user',$user['id']);
    Application::$app->session->set('user_name',$user['name']);
    Application::$app->session->set('username',$user['username']);
    Application::$app->session->setFlash('success', 'Login successful');
    // Redirect to user's subdomain locally, or relative path on Render
    if (is_production()) {
        $this->redirect('/dashboard');
    } else {
        $username = $user['username'];
        $this->redirect("http://{$username}.blogify.dev/dashboard");
    }
  }
  public function Logout(){
    Application::$app->session->remove('user');
    Application::$app->session->remove('user_name');
    Application::$app->session->remove('username');
    Application::$app->session->setFlash('success', 'Logged out successfully');
    
    if (is_production()) {
        $this->redirect('/');
    } else {
        $this->redirect('http://blogify.dev/');
    }
  }
}
