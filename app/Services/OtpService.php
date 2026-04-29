<?php
namespace App\Services;

use App\Foundation\Application;
use PDO;

class OtpService
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Application::$app->db->getConnection();
    }

    public function generateOtp(int $userId): string
    {
        $otp = (string)rand(100000, 999999);
        // Use gmdate() to store expires_at in UTC, matching MariaDB's UTC_TIMESTAMP()
        $expiresAt = gmdate('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Delete old OTPs for this user
        $stmt = $this->db->prepare("DELETE FROM otps WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);

        // Insert new OTP
        $stmt = $this->db->prepare("INSERT INTO otps (user_id, otp_code, expires_at) VALUES (:user_id, :otp_code, :expires_at)");
        $stmt->execute([
            'user_id' => $userId,
            'otp_code' => $otp,
            'expires_at' => $expiresAt
        ]);

        return $otp;
    }

    public function verifyOtp(int $userId, string $otp): bool
    {
        // Use UTC_TIMESTAMP() to match the UTC time stored by gmdate() in generateOtp()
        $stmt = $this->db->prepare("SELECT * FROM otps WHERE user_id = :user_id AND otp_code = :otp AND expires_at > UTC_TIMESTAMP()");
        $stmt->execute([
            'user_id' => $userId,
            'otp' => $otp
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Delete the used OTP
            $stmt = $this->db->prepare("DELETE FROM otps WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            return true;
        }

        return false;
    }
}
