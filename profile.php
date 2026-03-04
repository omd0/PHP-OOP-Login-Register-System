<?php
/**
 * Created by Chris on 9/29/2014 3:52 PM.
 */

require_once 'core/init.php';

if(!$username = Input::get('user')) {
    Redirect::to('index.php');
}

$profileUser = new User($username);

if(!$profileUser->exists()) {
    Redirect::to(404);
}

$user = new User();
$pageTitle = 'Profile - ' . escape($profileUser->data()->username);
require_once 'includes/header.php';

$data = $profileUser->data();
?>
<div class="card">
    <div class="card-body">
        <h3 class="card-title"><?php echo escape($data->username); ?></h3>
        <p class="card-text"><strong>Name:</strong> <?php echo escape($data->name); ?></p>
    </div>
</div>
<?php
require_once 'includes/footer.php';