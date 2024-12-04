<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 */

namespace App\Model;

use Nette;


/**
 * Passwords tools. Requires PHP >= 5.3.7.
 *
 * @author     David Grudl
 */
class Passwords
{
    const int BCRYPT_COST = 10;

    /**
     * Computes salted password hash.
     * @param string $password
     * @param array|null $options with cost (4-31), salt (22 chars)
     * @return string  60 chars long
     */
    public static function hash(string $password, ?array $options): string
    {
        $cost = isset($options['cost']) ? (int)$options['cost'] : self::BCRYPT_COST;
        $salt = isset($options['salt']) ? (string)$options['salt'] : Nette\Utils\Random::generate(22, '0-9A-Za-z./');

        if (PHP_VERSION_ID < 50307) {
            throw new Nette\NotSupportedException(__METHOD__ . ' requires PHP >= 5.3.7.');
        } elseif (($len = strlen($salt)) < 22) {
            throw new Nette\InvalidArgumentException("Salt must be 22 characters long, $len given.");
        } elseif ($cost < 4 || $cost > 31) {
            throw new Nette\InvalidArgumentException("Cost must be in range 4-31, $cost given.");
        }

        $hash = crypt($password, '$2y$' . ($cost < 10 ? 0 : '') . $cost . '$' . $salt);
        if (strlen($hash) < 60) {
            throw new Nette\InvalidStateException('Hash returned by crypt is invalid.');
        }
        return $hash;
    }

    /**
     * Verifies that a password matches a hash.
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verify(string $password, string $hash): bool
    {
        return preg_match('#^\$2y\$(?P<cost>\d\d)\$(?P<salt>.{22})#', $hash, $m)
            && $m['cost'] > 3 && $m['cost'] < 31
            && self::hash($password, $m) === $hash;
    }

    /**
     * Checks if the given hash matches the options.
     * @param string $hash
     * @param array|null $options with cost (4-31)
     * @return bool
     */
    public static function needsRehash(string $hash, ?array $options): bool
    {
        $cost = isset($options['cost']) ? (int)$options['cost'] : self::BCRYPT_COST;
        return !preg_match('#^\$2y\$(?P<cost>\d\d)\$(?P<salt>.{22})#', $hash, $m)
            || $m['cost'] < $cost;
    }

}
