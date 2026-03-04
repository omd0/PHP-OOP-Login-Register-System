<?php
/**
 * Sanitization and display helpers (learning project).
 * escape() prevents XSS; avatar helpers display user picture or initials.
 */

require_once 'core/init.php';

/**
 * Escape output for HTML to prevent XSS (Cross-Site Scripting).
 * Use this whenever you output user-supplied data to the page.
 */
function escape($string) {
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Get avatar URL for a user (stored filename) or null if no avatar set.
 * User object or stdClass must have ->avatar and ->id (e.g. $user->data()).
 */
function get_avatar_url($userData) {
    if (empty($userData->avatar)) {
        return null;
    }
    $path = 'uploads/avatars/' . $userData->avatar;
    return file_exists($path) ? $path : null;
}

/**
 * Get initials for fallback avatar (e.g. "John Doe" -> "JD").
 */
function get_avatar_initials($userData) {
    $name = isset($userData->name) ? trim($userData->name) : '';
    if ($name === '') {
        $name = isset($userData->username) ? trim($userData->username) : '?';
    }
    $parts = preg_split('/\s+/', $name, 2);
    if (count($parts) >= 2) {
        return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
    }
    return strtoupper(mb_substr($name, 0, 2));
}

/**
 * Output avatar HTML: image if available, otherwise a circle with initials.
 * Use in header, profile, and anywhere you show the user.
 *
 * @param object $userData User data (e.g. $user->data())
 * @param int    $size     Width/height in pixels
 * @param string $cssClass Optional extra CSS classes
 */
function avatar_html($userData, $size = 40, $cssClass = '') {
    $url = get_avatar_url($userData);
    $initials = get_avatar_initials($userData);
    $style = 'width:' . (int)$size . 'px;height:' . (int)$size . 'px;font-size:' . max(12, (int)$size / 2.5) . 'px;';
    $class = trim('avatar-circle ' . $cssClass);

    if ($url) {
        echo '<img src="' . escape($url) . '" alt="Avatar" class="' . escape($class) . '" style="' . $style . 'object-fit:cover;border-radius:50%;" loading="lazy">';
    } else {
        echo '<span class="' . escape($class) . ' avatar-initials" style="' . $style . '" title="' . escape($initials) . '">' . escape($initials) . '</span>';
    }
}