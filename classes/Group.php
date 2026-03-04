<?php
/**
 * Group model (OOP): account types from the `groups` table (learning project).
 * - Used at registration to let user choose Standard User vs Administrator.
 * - getAll(): fetch all groups for dropdowns.
 * - getById($id): fetch one group; hasPermission($id, $key) checks permissions JSON.
 * - isValidId($id): whether the id can be used when creating a user.
 */

class Group {
    private $_db;

    public function __construct() {
        $this->_db = DB::getInstance();
    }

    /**
     * Get all groups (for registration dropdown, etc.).
     * @return array List of stdClass objects (id, name, permissions).
     */
    public function getAll() {
        $this->_db->query("SELECT * FROM `groups` ORDER BY id ASC");
        $results = $this->_db->results();
        return $results ?: array();
    }

    /**
     * Get a single group by id.
     * @param int $id
     * @return object|null stdClass or null
     */
    public function getById($id) {
        $result = $this->_db->get('groups', array('id', '=', (int) $id));
        return $result->count() ? $result->first() : null;
    }

    /**
     * Check if a group has a permission key (e.g. 'admin').
     * @param int    $id  Group id
     * @param string $key Permission key
     * @return bool
     */
    public function hasPermission($id, $key) {
        $group = $this->getById($id);
        if (!$group || empty($group->permissions)) {
            return false;
        }
        $permissions = json_decode($group->permissions, true);
        return !empty($permissions[$key]);
    }

    /**
     * Whether this group id exists and is allowed for registration (OOP validation).
     * @param int $id
     * @return bool
     */
    public function isValidId($id) {
        $id = (int) $id;
        return $id > 0 && $this->getById($id) !== null;
    }
}
