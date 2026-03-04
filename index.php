<?php
/**
 * Created by Chris on 9/29/2014 3:42 PM.
 */

require_once 'core/init.php';

$page_title = 'Home';
$user = new User();

require_once 'includes/header.php';
?>

<?php if(Session::exists('home')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo escape(Session::flash('home')); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if($user->isLoggedIn()): ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title">
                Welcome back, <a href="profile.php?user=<?php echo escape($user->data()->username); ?>">
                    <?php echo escape($user->data()->username); ?>
                </a>!
            </h4>

            <?php if($user->hasPermission('admin')): ?>
                <span class="badge bg-danger mb-3">Administrator</span>
            <?php endif; ?>

            <ul class="list-group list-group-flush mt-3">
                <li class="list-group-item"><a href="update.php" class="text-decoration-none">Update Profile</a></li>
                <li class="list-group-item"><a href="changepassword.php" class="text-decoration-none">Change Password</a></li>
                <li class="list-group-item"><a href="logout.php" class="text-decoration-none text-danger">Log out</a></li>
            </ul>
        </div>
    </div>
<?php else: ?>
    <div class="text-center mt-5">
        <h2>Welcome to PHP OOP Auth</h2>
        <p class="text-muted">Please login or create an account to continue.</p>
        <a href="login.php" class="btn btn-primary me-2">Login</a>
        <a href="register.php" class="btn btn-outline-secondary">Register</a>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
