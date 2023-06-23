<?php declare(strict_types=1);

namespace App\Services;

use App\Exceptions\UserNotFoundException;
use App\Messages\Error;
use App\Models\User as UserModel;

final readonly class User
{
    /**
     * @param Database $db
     */
    public function __construct(
        private Database $db
    )
    {}

    /**
     * @param int $userId
     * @return UserModel
     * @throws UserNotFoundException
     */
    public function findByTelegramId(int $userId): UserModel
    {
        $data = $this->db->execute('SELECT * FROM users WHERE telegram_id = ?', [$userId]);
        if (empty($data)) {
            throw new UserNotFoundException(Error::UserNotFoundException->value);
        }

        return new UserModel(
            $data[0]['id'],
            $data[0]['telegram_id'],
            $data[0]['first_name']
        );
    }

    /**
     * @param int $telegramId
     * @param string $firstName
     * @return UserModel
     */
    public function create(
        int $telegramId,
        string $firstName
    ): UserModel
    {
        $userInfo = $this->db->execute(
            'INSERT INTO users (telegram_id, first_name) VALUES (?, ?) returning id',
            [$telegramId, $firstName]
        );
        $userId = $userInfo[0]['id'];
        return new UserModel(
            $userId,
            $telegramId,
            $firstName
        );
    }
}