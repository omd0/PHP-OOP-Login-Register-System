<?php
/**
 * User model: login, register, permissions, session, and profile updates (including avatar).
 * - Constructor: load current user from session, or find by id/username if passed.
 * - create() / update() use the DB layer; update() can set avatar filename.
 */

class User {
    private $_db;
    private $_data;           // current user row (stdClass)
    private $_sessionName;
    private $_cookieName;
    private $isLoggedIn;

    /**
     * @param int|string|null $user If null, try to load from session; else find by id or username.
     */
    public function __construct($user = null) {
        $this->_db = DB::getInstance();
        $this->_sessionName = Config::get('sessions/session_name');
        $this->_cookieName = Config::get('remember/cookie_name');

        if (!$user) {
            // No id/username given: restore login from session if present
            if (Session::exists($this->_sessionName)) {
                $user = Session::get($this->_sessionName);
                if ($this->find($user)) {
                    $this->isLoggedIn = true;
                }
            }
        } else {
            $this->find($user);
        }
    }

    /** Insert a new user row (used by register). */
    public function create($fields = array()) {
        if (!$this->_db->insert('users', $fields)) {
            throw new Exception('Sorry, there was a problem creating your account;');
        }
    }

    /**
     * Update user row. If $id is null and user is logged in, updates current user.
     * $fields can include 'name', 'password', 'avatar', etc.
     */
    public function update($fields = array(), $id = null) {
        if (!$id && $this->isLoggedIn()) {
            $id = $this->data()->id;
        }
        if (!$this->_db->update('users', $id, $fields)) {
            throw new Exception('There was a problem updating');
        }
    }

    /**
     * Find user by id (numeric) or username, load into _data.
     * @return bool true if found
     */
    public function find($user = null) {
        if ($user) {
            $field = is_numeric($user) ? 'id' : 'username';
            $data = $this->_db->get('users', array($field, '=', $user));
            if ($data->count()) {
                $this->_data = $data->first();
                return true;
            }
        }
        return false;
    }

    /**
     * Login: with username+password verify and set session; optionally set "remember me" cookie.
     * Called with no args when restoring from cookie (session not set yet).
     */
    public function login($username = null, $password = null, $remember = false) {
        if (!$username && !$password && $this->exists()) {
            Session::put($this->_sessionName, $this->data()->id);
        } else {
            if (!$this->find($username)) {
                return false;
            }
            if (!Hash::isValidPassword($password, $this->data()->password)) {
                return false;
            }
            Session::put($this->_sessionName, $this->data()->id);

            if ($remember) {
                $hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));
                $hash = $hashCheck->count() ? $hashCheck->first()->hash : Hash::unique();
                if (!$hashCheck->count()) {
                    $this->_db->insert('users_session', array('user_id' => $this->data()->id, 'hash' => $hash));
                }
                Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
            }
            return true;
        }
        return false;
    }

    /** Check if current user's group has a permission (e.g. 'admin'). */
    public function hasPermission($key) {
        $group = $this->_db->get('groups', array('id', '=', $this->data()->group));
        if ($group->count()) {
            $permissions = json_decode($group->first()->permissions, true);
            return !empty($permissions[$key]);
        }
        return false;
    }

    public function exists() {
        return !empty($this->_data);
    }

    /** Clear session and "remember me" cookie, delete row from users_session. */
    public function logout() {
        $this->_db->delete('users_session', array('user_id', '=', $this->data()->id));
        Session::delete($this->_sessionName);
        Cookie::delete($this->_cookieName);
    }

    /** Current user row (stdClass), e.g. for avatar_html($user->data()). */
    public function data() {
        return $this->_data;
    }

    public function isLoggedIn() {
        return $this->isLoggedIn;
    }
}