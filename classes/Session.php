<?php
/**
 * Session helpers: put, get, delete, and one-time flash messages (learning project).
 * flash($name, $string): set message for next page; flash($name): read and remove it.
 */

class Session {
    public static function exists($name) {
        return (isset($_SESSION[$name])) ? true : false;
    }

    public static function put($name, $value) {
        return $_SESSION[$name] = $value;
    }

    public static function get($name) {
        return $_SESSION[$name];
    }

    public static function delete($name) {
        if(self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }

    public static function flash ($name, $string = 'null') {
        if(self::exists($name)) {
            $session = self::get($name);
            self::delete($name);
                return $session;
        } else {
            self::put($name, $string);
        }
    }
}