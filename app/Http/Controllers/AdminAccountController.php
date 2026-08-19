<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class AdminAccountController extends Controller {
 public function edit(){return view('admin.account');}
 public function update(Request $r){
  $d=$r->validate(['current_password'=>['required','current_password'],'name'=>['required','string','max:120'],'email'=>['required','email','max:255'],'password'=>['nullable','string','min:12','confirmed']]);
  $u=$r->user();$u->name=$d['name'];$u->email=$d['email'];if(!empty($d['password']))$u->password=Hash::make($d['password']);$u->save();
  return back()->with('success','Administrator account updated.');
 }
}
