<?php
/**
 * Public profile page: show a user by username (learning project).
 * - URL: profile.php?user=username
 * - Displays avatar, name, username; "Update" link only for own profile.
 */

require_once 'core/init.php';

if (!$username = Input::get('user')) {
    Redirect::to('index.php');
}

$profileUser = new User($username);

if (!$profileUser->exists()) {
    Redirect::to(404);
}

$user = new User();
$pageTitle = 'Profile - ' . escape($profileUser->data()->username);
require_once 'includes/header.php';

$data = $profileUser->data();
$isOwnProfile = $user->isLoggedIn() && $user->data()->id === $data->id;
?>
<div class="card">
    <div class="card-body d-flex align-items-center gap-4">
        <?php avatar_html($data, 80); ?>
        <div>
            <h3 class="card-title mb-1"><?php echo escape($data->username); ?></h3>
            <p class="card-text text-muted mb-0"><strong>Name:</strong> <?php echo escape($data->name); ?></p>
            <?php if ($isOwnProfile): ?>
                <a href="update.php" class="btn btn-sm btn-outline-primary mt-2">Update profile &amp; avatar</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
require_once 'includes/footer.php';