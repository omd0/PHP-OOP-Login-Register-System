<?php
/**
 * Created by Chris on 9/29/2014 3:53 PM.
 */

require_once 'core/init.php';

$user = new User();

if(!$user->isLoggedIn()) {
    Redirect::to('index.php');
}

$changePasswordError = null;
$changePasswordErrors = array();

if(Input::exists()) {
    if(Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validate->check($_POST, array(
            'current_password' => array(
                'required' => true,
                'min' => 6
            ),
            'new_password' => array(
                'required' => true,
                'min' => 6
            ),
            'new_password_again' => array(
                'required' => true,
                'min' => 6,
                'matches' => 'new_password'
            )
        ));

        if($validate->passed()) {
            if(!Hash::isValidPassword(Input::get('current_password'), $user->data()->password)){
                $changePasswordError = 'Your current password is wrong.';
            } else {
                $user->update(array(
                    'password' => Hash::encryptPassword(Input::get('new_password'))
                ));

                Session::flash('home', 'Your password has been changed!');
                Redirect::to('index.php');
            }
        } else {
            $changePasswordErrors = $validate->errors();
        }
    }
}

$pageTitle = 'Change Password';
require_once 'includes/header.php';

if ($changePasswordError) {
    echo '<div class="alert alert-danger">' . escape($changePasswordError) . '</div>';
}
if (!empty($changePasswordErrors)) {
    echo '<div class="alert alert-danger"><ul class="mb-0">';
    foreach($changePasswordErrors as $error) {
        echo '<li>' . escape($error) . '</li>';
    }
    echo '</ul></div>';
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <h2 class="mb-4">Change Password</h2>
        <form action="" method="post">
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" name="current_password" id="current_password" class="form-control">
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" name="new_password" id="new_password" class="form-control">
            </div>
            <div class="mb-3">
                <label for="new_password_again" class="form-label">New Password Again</label>
                <input type="password" name="new_password_again" id="new_password_again" class="form-control">
            </div>
            <input type="hidden" name="token" id="token" value="<?php echo escape(Token::generate()); ?>">
            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>