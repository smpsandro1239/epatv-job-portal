<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated as Middleware;
use Illuminate\Http\Request;

class RedirectIfAuthenticated extends Middleware
{
  protected function redirectTo(Request $request): ?string
  {
    if (!$request->expectsJson()) {
      return route('home');
    }
    return null;
  }
}
