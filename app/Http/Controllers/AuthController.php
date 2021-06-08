<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use App\Services\UserService;
use App\Http\Requests\SignupRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    private $authService;
    private $userService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(SignupRequest $request)
    {
        $params = $request->validated();

        try {
            $data = $this->authService->register($params);
            return $this->success_response($data, "User registered successfully", 201);
        } catch(\Exception $e) {
            return $this->exception_response($e, "Signup couldn't be completed, please try again later");
        }
        
    }

    public function login(LoginRequest $request)
    {

        $error_msg = "Login Failed, Invalid Credentials";

        // Get user
        $user = $this->userService->get_user_by_email($request->input('email'));

        if (!$user) {
            return $this->error_response("User does not exist", $error_msg, 404);
        }

        if (! password_verify($request->input('password'), $user->password) ) {
            return $this->error_response("Email or Password Incorrect", $error_msg, 400);
        }

        $credentials = $request->only(["email", "password"]);

        $data = $this->authService->login($user, $credentials);

        if ($data == "unauthorized") {

            return $this->error_response("Access unauthorized", $error_msg, 401);
        }
        return $this->success_response($data, "Login Successful");
    }

    public function logout()
    {
        Auth::logout();
        return $this->success_response(true, "Successfully logged out");
    }
}
