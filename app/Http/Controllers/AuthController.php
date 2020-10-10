<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use Session;
use App\User;
 
 
class AuthController extends Controller
{
    public function showFormLogin()
    {
        if (Auth::check()){ 
            return redirect()->route('home');
        }
        return view('login');
    }
 
    public function login(Request $request)
    {
        $rules = [
            'email'                 => 'required|email',
            'password'              => 'required|string'
        ];
 
        $messages = [
            'email.required'        => 'Email must be field',
            'email.email'           => 'Invalid Email',
            'password.required'     => 'Password must be field',
            'password.string'       => 'Password must be a String'
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        }
 
        $data = [
            'email'     => $request->input('email'),
            'password'  => $request->input('password'),
        ];
 
        Auth::attempt($data);
 
        if (Auth::check()) { 
            return redirect()->route('home');
        } else { 
            Session::flash('error', 'Email atau password salah');
            return redirect()->route('login');
        }
 
    }
 
    public function showFormRegister()
    {
        return view('register');
    }
 
    public function register(Request $request)
    {
        $rules = [
            'name'                  => 'required|min:3|max:35',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|confirmed'
        ];
 
        $messages = [
            'name.required'         => 'Name must be field',
            'name.min'              => 'Minimum 3 Character',
            'name.max'              => 'Maximum 3 Character',
            'email.required'        => 'Email must be field',
            'email.email'           => 'Invalid Email',
            'email.unique'          => 'Email has been registered',
            'password.required'     => 'Password must be field',
            'password.confirmed'    => 'Password do not match'
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        }
 
        $user = new User;
        $user->name = ucwords(strtolower($request->name));
        $user->email = strtolower($request->email);
        $user->password = Hash::make($request->password);
        $user->email_verified_at = \Carbon\Carbon::now();
        $simpan = $user->save();
 
        if($simpan){
            Session::flash('success', 'Register Success');
            return redirect()->route('login');
        } else {
            Session::flash('errors', ['' => 'Register Failed']);
            return redirect()->route('register');
        }
    }
 
    public function logout()
    {
        Auth::logout(); 
        return redirect()->route('login');
    }
 
 
}