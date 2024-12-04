<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\InvalidArgumentException;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\SmartObject;
use Nette\Utils\Strings;


/**
 * Users management.
 */
class UserManager implements IAuthenticator
{
    use SmartObject;

    const TABLE_NAME = 'osoba';
    const COLUMN_ID = 'id';
    const COLUMN_NAME = 'email';
    const COLUMN_PASSWORD_HASH = 'password';
    const COLUMN_ROLE = 'typ_osoby';
    const PASSWORD_MAX_LENGTH = 4096;

    private Explorer $database;

    public function __construct(Explorer $explorer)
    {
        $this->database = $explorer;
    }


    /**
     * Performs an authentication.
     * @param array $credentials
     * @return Identity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials): Identity
    {
        list($username, $password) = $credentials;
        $row = $this->database->table(self::TABLE_NAME)
            ->where(self::COLUMN_NAME, $username)
            ->fetch();


        if (!$row) {
            throw new AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

        } elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
            throw new AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

        } elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
            $row->update(array(
                self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
            ));
        }

        $arr = $row->toArray();
        unset($arr[self::COLUMN_PASSWORD_HASH]);
        return new Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
    }


    /**
     * Adds new user.
     * @param string $username
     * @param string $password
     * @return void
     */
    public function add(string $username, string $password)
    {
        $this->database->table(self::TABLE_NAME)->insert(array(
            self::COLUMN_NAME => $username,
            self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
        ));
    }

    /**
     * Computes salted password hash.
     * @param string $password
     * @param array|null $options
     * @return string
     */
    public static function hashPassword(string $password, array $options = null): string
    {
        if ($password === Strings::upper($password)) { // perhaps caps lock is on
            $password = Strings::lower($password);
        }
        $password = substr($password, 0, self::PASSWORD_MAX_LENGTH);
        $options = $options ?: implode('$', array(
            'algo' => PHP_VERSION_ID < 50307 ? '$2a' : '$2y', // blowfish
            'cost' => '07',
            'salt' => Strings::random(22),
        ));
        return crypt($password, $options);
    }

    /**
     * Function generate new password in clear type
     * @param int $count new password length
     */
    public static function generateNewPassword(int $count = 16): string
    {
        //TODO: migrate to service layer

        // can't generate password with less than 16 characters
        if (!is_int($count) or ($count < 16)) {
            throw new InvalidArgumentException('Parameter must be type integer and length must be greater than sixteen characters!');
        }
        return Strings::random($count, 'A-Z0-9a-z');
    }

    /**
     * Verifies that a password matches a hash.
     * @param string $password
     * @param array|null $hash
     * @return bool
     */
    public static function verifyPassword(string $password, array $hash = null): bool
    {
        return self::hashPassword($password, $hash) === $hash

            || (PHP_VERSION_ID >= 50307 && substr($hash, 0, 3) === '$2a' && self::hashPassword($password, $tmp = '$2x' . substr($hash, 3)) === $tmp);
    }
}
