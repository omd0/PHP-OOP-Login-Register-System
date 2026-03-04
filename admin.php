<?php
/**
 * Admin dashboard (OOP): list all users; only accessible by users with admin permission.
 * - Uses Admin::requireAdmin($user) to enforce access.
 * - Uses Admin::getAllUsers() and Group for display (learning project).
 */

require_once 'core/init.php';

$user = new User();
$admin = new Admin();

// OOP guard: redirect if not logged in or not admin
Admin::requireAdmin($user);

$users = $admin->getAllUsers();
$pageTitle = 'Admin Dashboard';
require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Admin Dashboard</h2>
    <span class="badge bg-warning text-dark">Administrator</span>
</div>

<p class="text-muted">All registered users. Only visible to admin accounts.</p>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Account type</th>
                    <th>Joined</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $row): ?>
                <tr>
                    <td>
                        <?php
                        $fakeUser = (object) array(
                            'name' => $row->name,
                            'username' => $row->username,
                            'avatar' => isset($row->avatar) ? $row->avatar : null
                        );
                        avatar_html($fakeUser, 36);
                        ?>
                    </td>
                    <td><?php echo escape($row->username); ?></td>
                    <td><?php echo escape($row->name); ?></td>
                    <td>
                        <span class="badge <?php echo ($row->group_name === 'Administrator') ? 'bg-warning text-dark' : 'bg-secondary'; ?>">
                            <?php echo escape($row->group_name); ?>
                        </span>
                    </td>
                    <td><?php echo escape(date('M j, Y', strtotime($row->joined))); ?></td>
                    <td>
                        <a href="profile.php?user=<?php echo escape($row->username); ?>" class="btn btn-sm btn-outline-primary">View profile</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
