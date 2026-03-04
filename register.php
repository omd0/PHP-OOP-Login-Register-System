<?php
/**
 * Register page: create account with name, username, password, and account type (learning project).
 * - Account type is chosen via Group class (OOP); validated with Group::isValidId().
 * - Password is hashed (bcrypt) before storing; never save plain text.
 */

require_once 'core/init.php';

$user = new User();
$group = new Group();
$registerError = null;
$registerErrors = array();

if (Input::exists()) {
    if (Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validate->check($_POST, array(
            'name' => array(
                'name' => 'Name',
                'required' => true,
                'min' => 2,
                'max' => 50
            ),
            'username' => array(
                'name' => 'Username',
                'required' => true,
                'min' => 2,
                'max' => 20,
                'unique' => 'users'
            ),
            'password' => array(
                'name' => 'Password',
                'required' => true,
                'min' => 6
            ),
            'password_again' => array(
                'required' => true,
                'matches' => 'password'
            ),
        ));

        // OOP validation: account type must be a valid group id (e.g. 1 = Standard, 2 = Admin)
        $accountType = (int) Input::get('account_type');
        if (!$group->isValidId($accountType)) {
            $registerErrors[] = 'Please choose a valid account type.';
        }

        if ($validate->passed() && empty($registerErrors)) {
            try {
                $user->create(array(
                    'name' => Input::get('name'),
                    'username' => Input::get('username'),
                    'password' => Hash::encryptPassword(Input::get('password')),
                    'joined' => date('Y-m-d H:i:s'),
                    'group' => $accountType
                ));

                Session::flash('home', 'Welcome ' . Input::get('username') . '! Your account has been registered. You may now log in.');
                Redirect::to('index.php');
            } catch (Exception $e) {
                $registerError = $e->getMessage();
            }
        } else {
            $registerErrors = array_merge($registerErrors, $validate->errors());
        }
    }
}

$accountTypes = $group->getAll();

$pageTitle = 'Register';
require_once 'includes/header.php';

if (!empty($registerError)) {
    echo '<div class="alert alert-danger">' . escape($registerError) . '</div>';
}
if (!empty($registerErrors)) {
    echo '<div class="alert alert-danger"><ul class="mb-0">';
    foreach ($registerErrors as $error) {
        echo '<li>' . escape($error) . '</li>';
    }
    echo '</ul></div>';
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <h2 class="mb-4">Register</h2>
        <form action="" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" value="<?php echo escape(Input::get('name')); ?>" id="name" class="form-control">
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" value="<?php echo escape(Input::get('username')); ?>" class="form-control">
            </div>
            <div class="mb-3">
                <label for="account_type" class="form-label">Account type</label>
                <select name="account_type" id="account_type" class="form-select" required>
                    <?php foreach ($accountTypes as $g): ?>
                    <option value="<?php echo (int) $g->id; ?>" <?php echo (Input::get('account_type') === (string) $g->id) ? 'selected' : ''; ?>>
                        <?php echo escape($g->name); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Standard User or Administrator (admin can access the Admin dashboard).</small>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="mb-3">
                <label for="password_again" class="form-label">Password Again</label>
                <input type="password" name="password_again" id="password_again" class="form-control">
            </div>
            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
