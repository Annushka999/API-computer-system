<?php

namespace App\Http\Middleware;

use App\Models\File;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckOwnerFiles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $fileId = $request->route('file');

        $file = File::query()->where('file_id', $fileId)->first();
        $userToRemove = User::query()->where('email', $request->email)->first();

        if (!$file || $file->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Forbidden for you'
            ], 403, ['Content-type' => 'application/json']);
        }

        if ($userToRemove->id == Auth::id()) {
            return response()->json([
                'message' => 'Forbidden for you'
            ], 403, ['Content-type' => 'application/json']);
        }

        return $next($request);
    }
}
