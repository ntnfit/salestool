<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Hash;
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
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
        ]);
        $input = $request->all();

      
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }
        $user = User::find(Auth::id());
        $user->update($input);
        return redirect()->route('profiles')
        ->with('success','Update information successfully');
    }

    public function updatepass(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|same:confirm-password'
        ]);
        $input = $request->all();

      
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }
        $user = User::find(Auth::id());
        $user->update($input);
        return redirect()->route('profiles')
        ->with('success','updated password successfully');
    }

}
