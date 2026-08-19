<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request; use Illuminate\Support\Facades\Auth;
class AdminAuthController extends Controller {
 public function showLogin(){ if(Auth::check() && Auth::user()->is_admin)return redirect()->route('admin.dashboard'); return view('admin.login'); }
 public function login(Request $request){
  $c=$request->validate(['email'=>['required','email'],'password'=>['required','string']]);
  if(!Auth::attempt(['email'=>$c['email'],'password'=>$c['password'],'is_admin'=>true],$request->boolean('remember')))
   return back()->withErrors(['email'=>'The administrator credentials are incorrect.'])->onlyInput('email');
  $request->session()->regenerate(); return redirect()->intended(route('admin.dashboard'));
 }
 public function logout(Request $request){Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();return redirect()->route('admin.login');}
}
