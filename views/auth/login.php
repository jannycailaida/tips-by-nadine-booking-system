<!-- Login Page -->
<section class="page-header auth-header">
    <div class="container">
        <h1 class="page-title">Welcome Back</h1>
        <p class="page-subtitle">Sign in to manage your bookings</p>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Sign In</h2>
                <p>Enter your credentials to access your account</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error" role="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form" novalidate>
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-input" required autocomplete="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-input" required autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>

            <p class="auth-footer">
                Don't have an account? <a href="<?php echo base_url('register.php'); ?>">Sign up</a>
            </p>
        </div>
    </div>
</section>