<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Security\Passwords;
use Nette\Database\Table\Selection;
use Nette\SmartObject;
use Nette\Security\Authenticator;
use Nette\Security\SimpleIdentity;
use Nette\Security\AuthenticationException;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Database\Explorer;

/**
 * Users management.
 */
final class UserManager implements Authenticator
{
    use SmartObject;

    private const
        TABLE_NAME = 'users',
        COLUMN_ID = 'user_id',
        COLUMN_PASSWORD_HASH = 'password',
        COLUMN_EMAIL = 'email',
        COLUMN_FIRSTNAME = 'firstname',
        COLUMN_LASTNAME = 'lastname',
        COLUMN_ROLE = 'role';

    /** @var Explorer */
    private $database;

    /** @var Passwords */
    private $passwords;

    public function __construct(Explorer $database, Passwords $passwords)
    {
        $this->database = $database;
        $this->passwords = $passwords;
    }

    /**
     * Performs an authentication.
     * @throws AuthenticationException
     */
    public function authenticate($user, $password): SimpleIdentity
    {

        $row = $this->database->table(self::TABLE_NAME)
            ->where(self::COLUMN_EMAIL, $user)
            ->fetch();

        if (!$row) {
            throw new AuthenticationException('Zadali jste nesprávný email.', self::IDENTITY_NOT_FOUND);
        } elseif (!$this->passwords->verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
            throw new AuthenticationException('Vaše heslo není správné.', self::INVALID_CREDENTIAL);
        } elseif ($this->passwords->needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
            $row->update([
                self::COLUMN_PASSWORD_HASH => $this->passwords->hash($password),
            ]);
        }

        $arr = $row->toArray();
        unset($arr[self::COLUMN_PASSWORD_HASH]);
        return new SimpleIdentity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
    }

    public function add(string $firstname, string $lastname, string $email, string $password, string $role): void {
        Nette\Utils\Validators::assert($email, 'email');
        try {
            $this->database->table(self::TABLE_NAME)->insert([
                self::COLUMN_FIRSTNAME => $firstname,
                self::COLUMN_LASTNAME => $lastname,
                self::COLUMN_PASSWORD_HASH => $this->passwords->hash($password),
                self::COLUMN_EMAIL => $email,
                self::COLUMN_ROLE => $role,
            ]);
        } catch (UniqueConstraintViolationException $e) {

        }
    }

    public function getUsers(): Selection {
        return $this->database->table(self::TABLE_NAME);
    }

    public function removeUser(int $id) {
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $id)->delete();
    }
}