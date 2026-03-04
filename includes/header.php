<?php
/**
 * Site header: nav, avatar in navbar, and shared styles.
 * $user and $pageTitle should be set by the including page.
 */
if (!isset($user)) {
    $user = new User();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? escape($pageTitle) . ' - ' : ''; ?>Login System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        /* Avatar: circle with image or initials (used in nav, profile, home) */
        .avatar-circle { display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; flex-shrink: 0; }
        .avatar-initials { background: rgba(255,255,255,0.25); color: #fff; font-weight: 600; }
        .nav-avatar { margin-right: 0.5rem; vertical-align: middle; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Login System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if (isset($user) && $user->isLoggedIn()): ?>
                        <li class="nav-item d-flex align-items-center">
                            <?php avatar_html($user->data(), 32, 'nav-avatar'); ?>
                            <a class="nav-link" href="profile.php?user=<?php echo escape($user->data()->username); ?>">Profile</a>
                        </li>
                        <?php if ($user->hasPermission('admin')): ?>
                        <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="update.php">Update</a></li>
                        <li class="nav-item"><a class="nav-link" href="changepassword.php">Change Password</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Log out</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container py-4">
