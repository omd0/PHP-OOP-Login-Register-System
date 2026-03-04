<?php
/**
 * Created by Chris on 9/29/2014 3:52 PM.
 */

require_once 'core/init.php';

if(!$username = Input::get('user')) {
    Redirect::to('index.php');
}

$user = new User($username);

if(!$user->exists()) {
    Redirect::to(404);
}

$data = $user->data();
$page_title = escape($data->username) . "'s Profile";

require_once 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><?php echo escape($data->username); ?></h4>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Name</dt>
                    <dd class="col-sm-8"><?php echo escape($data->name); ?></dd>

                    <dt class="col-sm-4">Username</dt>
                    <dd class="col-sm-8"><?php echo escape($data->username); ?></dd>

                    <dt class="col-sm-4">Joined</dt>
                    <dd class="col-sm-8"><?php echo escape($data->joined); ?></dd>
                </dl>
            </div>
            <div class="card-footer">
                <a href="index.php" class="btn btn-secondary btn-sm">Back to Home</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
