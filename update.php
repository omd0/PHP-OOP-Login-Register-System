<?php
/**
 * Update Profile page: change name and upload avatar (learning project).
 * - POST: validate name + optional image upload, then update user and redirect.
 * - Avatar: stored in uploads/avatars/; DB column "avatar" required (run db_migration_avatar.sql).
 */

require_once 'core/init.php';

$user = new User();

if (!$user->isLoggedIn()) {
    Redirect::to('index.php');
}

$updateError = null;
$updateErrors = array();

if (Input::exists()) {
    if (Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validate->check($_POST, array(
            'name' => array(
                'required' => true,
                'min' => 2,
                'max' => 50
            )
        ));

        if ($validate->passed()) {
            $fields = array('name' => Input::get('name'));

            // Optional avatar upload: validate and store file (use $_FILES for file inputs)
            if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $allowed = array('image/jpeg', 'image/png', 'image/gif');
                $maxSize = 2 * 1024 * 1024; // 2 MB
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($_FILES['avatar']['tmp_name']);

                if (!in_array($mime, $allowed)) {
                    $updateError = 'Avatar must be a JPG, PNG or GIF image.';
                } elseif ($_FILES['avatar']['size'] > $maxSize) {
                    $updateError = 'Avatar must be under 2 MB.';
                } else {
                    $ext = (array('image/jpeg' => '.jpg', 'image/png' => '.png', 'image/gif' => '.gif'))[$mime];
                    $filename = $user->data()->id . '_' . bin2hex(random_bytes(4)) . $ext;
                    $dir = 'uploads/avatars';
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dir . '/' . $filename)) {
                        $old = $user->data()->avatar ?? '';
                        if ($old && file_exists($dir . '/' . $old)) {
                            @unlink($dir . '/' . $old);
                        }
                        $fields['avatar'] = $filename;
                    } else {
                        $updateError = 'Could not save avatar.';
                    }
                }
            }

            if ($updateError === null) {
                try {
                    $user->update($fields);
                    Session::flash('home', 'Your details have been updated.');
                    Redirect::to('index.php');
                } catch (Exception $e) {
                    $updateError = $e->getMessage();
                }
            }
        } else {
            $updateErrors = $validate->errors();
        }
    }
}

$pageTitle = 'Update Profile';
require_once 'includes/header.php';

if ($updateError) {
    echo '<div class="alert alert-danger">' . escape($updateError) . '</div>';
}
if (!empty($updateErrors)) {
    echo '<div class="alert alert-danger"><ul class="mb-0">';
    foreach ($updateErrors as $error) {
        echo '<li>' . escape($error) . '</li>';
    }
    echo '</ul></div>';
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h2 class="mb-4">Update Profile</h2>
        <p class="text-muted small">Update your display name and optional profile picture (avatar).</p>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" value="<?php echo escape($user->data()->name); ?>" class="form-control">
            </div>
            <div class="mb-3">
                <label for="avatar" class="form-label">Avatar (optional)</label>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <?php avatar_html($user->data(), 48); ?>
                    <input type="file" name="avatar" id="avatar" class="form-control form-control-sm" accept="image/jpeg,image/png,image/gif">
                </div>
                <small class="text-muted">JPG, PNG or GIF, max 2 MB. Leave empty to keep current.</small>
            </div>
            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
