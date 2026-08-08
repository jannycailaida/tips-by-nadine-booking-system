<!-- Register Page -->
<section class="page-header auth-header">
    <div class="container">
        <h1 class="page-title">Create Account</h1>
        <p class="page-subtitle">Join Tips by Nadine for easy booking</p>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Register</h2>
                <p>Create your account to start booking</p>
            </div>

            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-error" role="alert">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form" novalidate>
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-input" required autocomplete="given-name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-input" required autocomplete="family-name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-input" required autocomplete="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number (Optional)</label>
                    <input type="tel" name="phone" id="phone" class="form-input" autocomplete="tel" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>

                <?php $referralValue = $_POST['referral_code'] ?? ($referralCode ?? ''); ?>
                <div class="form-group referral-field">
                    <label for="referral_code" class="form-label">Referral Code (Optional)</label>
                    <input type="text" name="referral_code" id="referral_code" class="form-input" autocomplete="off" value="<?php echo htmlspecialchars($referralValue); ?>" placeholder="TBN...">
                    <small class="form-hint">If a friend shared a code, enter it here. Referral credits are reviewed by the salon team.</small>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-input" required autocomplete="new-password" minlength="8">
                    <small class="form-hint">At least 8 characters</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-input" required autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
            </form>

            <p class="auth-footer">
                Already have an account? <a href="<?php echo base_url('login.php'); ?>">Sign in</a>
            </p>
        </div>
    </div>
</section>