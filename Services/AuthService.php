<?php
namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Database\DatabaseManager;

class AuthService
{
    private $database;

    public function __construct(DatabaseManager $database)
    {
        $this->database = $database;
    }

    public function register(array $params)
    {
        $plain_password = $params['password'];
        $params['password'] = app('hash')->make($params['password']);
        // In transaction so it doesn't create user if notification fails to send
        $this->database->beginTransaction();
        try {
            $user = User::create($params);
            // Send user sign up notification
            $data = [
                "email" => $user->email,
                "first_name" => $user->first_name,
                "last_name" => $user->last_name
            ];

            $credentials = ["email" => $user->email, "password" => $plain_password];
            $logged_in_user = $this->login($user, $credentials);
            $this->database->commit();
            return $logged_in_user;

        } catch (\Exception $e) {
            $this->database->rollBack();
            throw $e;
        }
    }

    public function login(User $user)
    {
        try {
            $user = $user->toArray();
            
            return $user;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}