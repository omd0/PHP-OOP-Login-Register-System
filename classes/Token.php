<?php
/**
 * CSRF token: generate a one-time token for forms, check on submit (learning project).
 * - generate(): store in session and return value for <input type="hidden" name="token">.
 * - check($token): compare with session and delete so form can't be replayed.
 */

class Token {
    public static function generate() {
        return Session::put(Config::get('sessions/token_name'), md5(uniqid()));
    }

    public static function check($token) {
        $tokenName = Config::get('sessions/token_name');

        if(Session::exists($tokenName) && $token === Session::get($tokenName)) {
            Session::delete($tokenName);
            return true;
        }

        return false;
    }
}