<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated as Middleware;

class RedirectIfAuthenticated extends Middleware
{
  protected function redirectTo($request)
  {
    if (!$request->expectsJson()) {
      return route('home');
    }
  }
}
