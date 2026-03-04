<?php
/**
 * Admin layer (OOP): protect admin-only pages and provide admin actions (learning project).
 * - requireAdmin($user): redirect to index with error if user is not logged in or not admin.
 * - getAllUsers(): return all users with their group name (for admin dashboard).
 */

class Admin {
    private $_db;

    public function __construct() {
        $this->_db = DB::getInstance();
    }

    /**
     * Ensure the current user is logged in and has admin permission; otherwise redirect.
     * Call this at the top of every admin page.
     *
     * @param User $user Current user (e.g. new User())
     * @return void Exits with redirect if not allowed
     */
    public static function requireAdmin(User $user) {
        if (!$user->isLoggedIn()) {
            Session::flash('home', 'You must be logged in to access that page.');
            Redirect::to('index.php');
        }
        if (!$user->hasPermission('admin')) {
            Session::flash('home', 'You do not have permission to access the admin area.');
            Redirect::to('index.php');
        }
    }

    /**
     * Get all users with their group name (for listing in admin dashboard).
     * Uses a simple JOIN via raw query since we need data from both users and groups.
     *
     * @return array List of objects with user fields + group_name
     */
    public function getAllUsers() {
        $sql = "SELECT u.id, u.username, u.name, u.joined, u.`group`, g.name AS group_name
                FROM users u
                LEFT JOIN `groups` g ON u.`group` = g.id
                ORDER BY u.joined DESC";
        $this->_db->query($sql);
        $results = $this->_db->results();
        return $results ?: array();
    }
}
