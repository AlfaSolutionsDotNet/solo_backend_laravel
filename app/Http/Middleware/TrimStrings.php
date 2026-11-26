<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array<int, string>
     */
    protected function transform($key, $value)
    {
        if ($key === 'desc' && request()->is('api/addPost')) {
            return $value;
        }

        return parent::transform($key, $value);
    }

    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}
