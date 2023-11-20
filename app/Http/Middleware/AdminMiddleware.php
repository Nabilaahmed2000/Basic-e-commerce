<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       //check if user is admin
       $user_id=Auth::id();
         $user=User::find($user_id);
            if($user->is_admin==true){
                return $next($request);
            }
        abort(403, 'Unauthorized action.');
    }
}
