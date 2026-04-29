<?php
namespace App\Http\Controllers;

use App\Foundation\Application;
use App\Models\User;
use App\Services\OtpService;

class VerificationController extends BaseController
{
    public function showVerifyForm()
    {
        $userId = Application::$app->session->get('pending_verification_user_id');
        if (!$userId) {
            $this->redirect('/login');
            return;
        }

        $this->render('VerifyOtp', ['blogOwner' => null], 'auth');
    }

    public function verify()
    {
        $userId = Application::$app->session->get('pending_verification_user_id');
        if (!$userId) {
            $this->redirect('/login');
            return;
        }

        $otp = $_POST['otp'] ?? '';
        if (empty($otp)) {
            Application::$app->session->setFlash('error', 'OTP is required');
            $this->redirect('/verify-otp');
            return;
        }

        $otpService = new OtpService();

        // OTP_BYPASS=true in .env skips verification — for presentations only
        $bypass = filter_var($_ENV['OTP_BYPASS'] ?? 'false', FILTER_VALIDATE_BOOLEAN);

        if ($bypass || $otpService->verifyOtp($userId, $otp)) {
            $userModel = new User(Application::$app->db);
            $userModel->verifyUser($userId);

            Application::$app->session->remove('pending_verification_user_id');
            Application::$app->session->setFlash('success', 'Phone number verified successfully. You can now login.');
            $this->redirect('/login');
        } else {
            Application::$app->session->setFlash('error', 'Invalid or expired OTP');
            $this->redirect('/verify-otp');
        }
    }
}
