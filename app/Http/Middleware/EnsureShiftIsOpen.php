<?php

namespace App\Http\Middleware;

use App\Models\Shift;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShiftIsOpen
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $openShift = Shift::open()->first();

        if (! $openShift) {
            // Check if the request expects JSON (for API or Livewire sometimes)
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No open shift.'], 403);
            }

            // Redirect to shift management if there's no open shift
            // Using a flash message or just redirecting
            return redirect()->route('shifts.index')->with('error', 'You must open a shift before accessing the POS.');
        }

        return $next($request);
    }
}
