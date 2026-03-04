<?php
/**
 * Created by Chris on 9/29/2014 3:42 PM.
 */

require_once 'core/init.php';

$user = new User();
$pageTitle = 'Home';
require_once 'includes/header.php';

if(Session::exists('home')) {
    echo '<div class="alert alert-success">' . Session::flash('home') . '</div>';
}

if($user->isLoggedIn()) {
?>
    <p class="lead">Hello, <a href="profile.php?user=<?php echo escape($user->data()->username);?>"><?php echo escape($user->data()->username); ?></a></p>
    <?php if($user->hasPermission('admin')): ?>
        <p class="badge bg-warning text-dark">Administrator</p>
    <?php endif; ?>
<?php
} else {
    echo '<p class="lead">You need to <a href="login.php">login</a> or <a href="register.php">register</a>.</p>';
}

require_once 'includes/footer.php';