<?php

namespace App\Service;

// Generate a random password string

class PasswordGenerator
{
    private const CHAR_LISTS = [
        'lower' => 'abcdefghijklmnopqrstuvwxyz',
        'upper' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'digits' => '0123456789',
        'symbols' => '!@#$%^?'
    ];

    public function generatePassword(int $length = 12, bool $includeSymbols = true): string
    {   
        // Get all charactere in a string
        $chars = self::CHAR_LISTS['lower'] . self::CHAR_LISTS['upper'] . self::CHAR_LISTS['digits'];
        if ($includeSymbols) {
            $chars .= self::CHAR_LISTS['symbols'];
        }

        $password = [];
        $charListLength = strlen($chars) - 1;

        // For security reasons: I enforce the presence of each element in the password
        $password[] = self::CHAR_LISTS['lower'][random_int(0, strlen(self::CHAR_LISTS['lower']) - 1)];
        $password[] = self::CHAR_LISTS['upper'][random_int(0, strlen(self::CHAR_LISTS['upper']) - 1)];
        $password[] = self::CHAR_LISTS['digits'][random_int(0, strlen(self::CHAR_LISTS['digits']) - 1)];
        if ($includeSymbols) {
            $password[] = self::CHAR_LISTS['symbols'][random_int(0, strlen(self::CHAR_LISTS['symbols']) - 1)];
        }

        // The rest is filled randomly with $chars elements
        $remainingLength = $length - count($password);
        for ($i = 0; $i < $remainingLength; $i++) {
            $password[] = $chars[random_int(0, $charListLength)];
        }

        // Mix everything
        shuffle($password);

        // return random password in string
        return implode('', $password);
    }
}
