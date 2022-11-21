<?php

namespace App\Http\Controllers\API;

class UserController
{
    public static function index()
    {
        return UserFacade::index();
    }
}
