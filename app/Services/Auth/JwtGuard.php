<?php

namespace App\Services\Auth;

use App\Services\TokenService;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class JwtGuard implements Guard
{
    use GuardHelpers;

    private $tokenService;
    private $bearerToken;
    private $isValid;

    public function __construct($bearerToken, TokenService $tokenService)
    {
        $this->bearerToken = $bearerToken;
        $this->tokenService = $tokenService;
        $this->isValid = $this->isValid();
    }

    /**
     * 检查 token 是否有效
     * @return bool
     */
    private function isValid()
    {
        return $this->tokenService->isValid($this->bearerToken);
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return $this->isValid;
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !$this->isValid;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        //Not Implemented
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        return $this->tokenService->getUserId($this->bearerToken);
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        //Not Implemented
    }

    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function setUser(Authenticatable $user)
    {
        //Not Implemented
    }
}
