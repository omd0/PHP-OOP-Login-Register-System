<?php
/**
 * Home page: welcome message, avatar, and login/register prompt (learning project).
 * - Logged in: show greeting with avatar and link to profile; admin badge if applicable.
 * - Guest: prompt to login or register.
 */

require_once 'core/init.php';

$user = new User();
$pageTitle = 'Home';
require_once 'includes/header.php';

// One-time flash message (e.g. after login or update)
if (Session::exists('home')) {
    echo '<div class="alert alert-success">' . escape(Session::flash('home')) . '</div>';
}

if ($user->isLoggedIn()) {
?>
    <div class="d-flex align-items-center gap-3 mb-3">
        <?php avatar_html($user->data(), 56); ?>
        <div>
            <p class="lead mb-0">Hello, <a href="profile.php?user=<?php echo escape($user->data()->username); ?>"><?php echo escape($user->data()->username); ?></a></p>
            <?php if ($user->hasPermission('admin')): ?>
                <span class="badge bg-warning text-dark">Administrator</span>
            <?php endif; ?>
        </div>
    </div>
<?php
} else {
    echo '<p class="lead">You need to <a href="login.php">login</a> or <a href="register.php">register</a>.</p>';
}

require_once 'includes/footer.php';