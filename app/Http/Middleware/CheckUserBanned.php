<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isBanned()) {
            $ban = auth()->user()->activeBan()->first();
            
            if ($ban) {
                $daysRemaining = $ban->daysRemaining();
                $reason = $ban->reason;
                
                auth()->logout();
                
                return redirect()->route('login')->with('error', 
                    "Your account has been banned for {$daysRemaining} more days. Reason: {$reason}"
                );
            }
        }

        return $next($request);
    }
}
