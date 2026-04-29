<section class="auth-section">
<div class="auth-card">
    <h2>Verify Your Phone</h2>
    <p>Please enter the 6-digit OTP sent to your phone number.</p>
    
    <form method="POST" action="/verify-otp">
        <div class="form-group">
            <label>OTP Code</label>
            <input type="text" name="otp" maxlength="6" pattern="\d{6}" placeholder="XXX-XXX" required>
        </div>
        <button type="submit" class="btn-primary full">Verify OTP</button>
    </form>
    
    <p class="auth-link">Didn't receive the code? <a href="/login">Try logging in again</a> to resend.</p>
</div>
</section>
