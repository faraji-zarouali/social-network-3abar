<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{   
    public function storeAvatar(Request $request) {
        $request->validate([
            'avatar' => 'required|image|max:4000',
        ]);
        $user = auth()->user();
        $filename = $user->id . '-' . uniqid() . '.jpg';
        $imgData = Image::make($request->file('avatar'))->resize(120, null, function ($constraint) {$constraint->aspectRatio();})->encode('jpg');        
        Storage::put('public/avatars/'.  $filename, $imgData);
        $oldAvatar = $user->avatar;
        $user->avatar = $filename;
        $user->save();

        if ($oldAvatar != "/fallback-avatar.jpg") {
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }

        return back()->with('success','Your Avatar Has been Updated.');
    } 

    public function AvatarForm() {
        return view('avatar-form');
    }

    public function profile(User $user){
        return view('profile-posts',['avatar' => $user->avatar , 'username' => $user->username , 'posts' => $user->posts()->latest()->get(), 'count' => $user->posts()->count()]);
    }

    public function ShowCorrectHomePage(){
        if (auth()->check()) {
            return view('homepage-feed');
        }else {
            return view('homepage');
        }
    }

    public function logout(Request $request){
        auth()->logout();
        return redirect('/')->with('success','you are logged out.');
    }

    public function login(Request $request){
        $incomingData = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required',
        ]);
        if (auth()->attempt(['username' => $incomingData['loginusername'],'password' => $incomingData['loginpassword']])) {
            $request->session()->regenerate();
            return redirect('/')->with('success','you are logged in.');
        }else {
            return redirect('/')->with('failure', 'invalid login.');
        }
    }

    public function register(Request $request){
        $incomingData = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed']
        ]);
        $incomingData['password'] = bcrypt($incomingData['password']);
        $user = User::create($incomingData);
        auth()->login($user);
        return redirect('/')->with('success','Thank you for creating an account.');
    }
}
