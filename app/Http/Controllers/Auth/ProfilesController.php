<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class ProfilesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function getUserByUsername($userid)
    {
        return User::whereid($userid)->firstOrFail();
    }
    public function show()
    {
        try {
            $user = $this->getUserByUsername(Auth::id());
        } catch (ModelNotFoundException $exception) {
            abort(404);
        }

        $data = [
            'user'         => $user
        ];

        return view('users.profile')->with($data);
    }

}
