<?php
/**
 * Login page: form + validation + optional "Remember me" (learning project).
 * - POST with valid token: validate username/password, then User::login() and redirect or show error.
 */

require_once 'core/init.php';

$user = new User();
$loginError = null;
$loginErrors = array();

// Only process when form was submitted (POST)
if (Input::exists()) {
    // CSRF protection: token must match the one stored in session when form was shown
    if (Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validate->check($_POST, array(
            'username' => array('required' => true),
            'password' => array('required' => true)
        ));

        if ($validate->passed()) {
            $remember = (Input::get('remember') === 'on');
            $login = $user->login(Input::get('username'), Input::get('password'), $remember);

            if ($login) {
                Redirect::to('index.php');
            } else {
                $loginError = 'Incorrect username or password';
            }
        } else {
            $loginErrors = $validate->errors();
        }
    }
}

$pageTitle = 'Login';
require_once 'includes/header.php';

if ($loginError) {
    echo '<div class="alert alert-danger">' . escape($loginError) . '</div>';
}
if (!empty($loginErrors)) {
    echo '<div class="alert alert-danger"><ul class="mb-0">';
    foreach($loginErrors as $error) {
        echo '<li>' . escape($error) . '</li>';
    }
    echo '</ul></div>';
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <h2 class="mb-4">Login</h2>
        <form action="" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="<?php echo escape(Input::get('username')); ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label for="remember" class="form-check-label">Remember me</label>
            </div>
            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
