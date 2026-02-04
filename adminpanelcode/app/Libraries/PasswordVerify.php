<?php 

namespace App\Libraries;

class PasswordVerify {
    
    /**
     * Hash the password using bcrypt
     */
    public static function hash($password){
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verify the given password against the hashed password
     */
    public static function verify($password, $hashedPassword){
        return password_verify($password, $hashedPassword);
    }
}
