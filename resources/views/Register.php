<!--
<form method="POST" action="/register">
    <input type="text" name="name" placeholder="Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
</form>
-->
<section class="auth-section">
<div class="auth-card">
<h2>Create Account</h2>
<form method="POST" action="/register">
<div class="form-group">
<label>Full Name</label>
<input type="text" name="name" required>
</div>
<div class="form-group">
<label>Username</label>
<input type="text" name="username" placeholder="lower case, no spaces" required>
</div>
<div class="form-group">
<label>Email</label>
<input type="email" name="email" required>
</div>
<div class="form-group">
<label>Password</label>
<input type="password" name="password" required>
</div>
<div class="form-group">
<label>Phone Number</label>
<input type="tel" name="phone" placeholder="+1234567890" required>
<small>Enter phone number with country code for OTP.</small>
</div>
<button type="submit" class="btn-primary full">Create Account</button>
</form>
<p class="auth-link">Already have an account? <a href="/login">Login</a></p>
</div>
</section>
