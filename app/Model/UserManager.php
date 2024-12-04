<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\InvalidArgumentException;
use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator;
use Nette\Security\SimpleIdentity;
use Nette\Utils\Random;
use Nette\Utils\Strings;


/**
 * Users management.
 */
class UserManager implements Authenticator
{
    const string TABLE_NAME = 'osoba';
    const string COLUMN_ID = 'id';
    const string COLUMN_NAME = 'email';
    const string COLUMN_PASSWORD_HASH = 'password';
    const string COLUMN_ROLE = 'typ_osoby';
    const int PASSWORD_MAX_LENGTH = 4096;

    private Explorer $database;

    public function __construct(Explorer $explorer)
    {
        $this->database = $explorer;
    }


    /**
     * Performs an authentication.
     * @param string $username
     * @param string $password
     * @return SimpleIdentity
     * @throws AuthenticationException
     */
    public function authenticate(string $username, string $password): SimpleIdentity
    {
        $row = $this->database->table(self::TABLE_NAME)
            ->where(self::COLUMN_NAME, $username)
            ->fetch();


        if (!$row) {
            throw new AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

        } elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
            throw new AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

        } elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH], null)) {
            $row->update(array(
                self::COLUMN_PASSWORD_HASH => Passwords::hash($password, null),
            ));
        }

        $arr = $row->toArray();
        unset($arr[self::COLUMN_PASSWORD_HASH]);
        return new SimpleIdentity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
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
            self::COLUMN_PASSWORD_HASH => Passwords::hash($password, null),
        ));
    }

    /**
     * Computes salted password hash.
     * @param string $password
     * @param array|null $options
     * @return string
     */
    public static function hashPassword(string $password, ?array $options): string
    {
        if ($password === Strings::upper($password)) { // perhaps caps lock is on
            $password = Strings::lower($password);
        }
        $password = substr($password, 0, self::PASSWORD_MAX_LENGTH);
        $options = $options ?: implode('$', array(
            'algo' => PHP_VERSION_ID < 50307 ? '$2a' : '$2y', // blowfish
            'cost' => '07',
            'salt' => Random::generate(22),
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
        return Random::generate($count, 'A-Z0-9a-z');
    }

    /**
     * Verifies that a password matches a hash.
     * @param string $password
     * @param array|null $hash
     * @return bool
     */
    public static function verifyPassword(string $password, ?array $hash): bool
    {
        return self::hashPassword($password, $hash) === $hash

            || (PHP_VERSION_ID >= 50307 && str_starts_with($hash, '$2a') && self::hashPassword($password, $tmp = '$2x' . substr($hash, 3)) === $tmp);
    }
}
