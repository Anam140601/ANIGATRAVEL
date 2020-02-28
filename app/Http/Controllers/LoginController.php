<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator;
use Hash;
use Illuminate\Support\Facades\Hash as FacadesHash;
use Illuminate\Support\Str;
use App\Penumpang;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('authentication.login');
    }
    public function login(Request $req)
    {
        $pt = User::where('username',$req->username)->count();
        $us = Penumpang::where('username',$req->username)->count();

        if ($us>0 || $pt>0) {
            if ($us>0) {
                $use = Penumpang::where('username',$req->username)->first();
                if (FacadesHash::check($req->password,$use->password)) {
                    $token = Str::random(60);
                    Penumpang::where('username',$req->username)->update(['token'=>$token]);
                    $level  = Penumpang::where('username',$req->username)->first();
                    session(['username'=>$req->username]);
                    session(['token'=>$token]);
                    session(['level'=>"user"]);
                    $data = ['body'=>'Success'];
                    return response(json_encode($data),200)->header('Content-Type','text/plain');
                } else {
                    $data = ['body'=>'Password Wrong'];
                    return response(json_encode($data),421)->header('Content-Type','text/plain');
                }
            } else if ($pt>0){
                $pet = User::where('username',$req->username)->first();
                if (FacadesHash::check($req->password,$pet->password)) {
                    $token = Str::random(60);
                    User::where('username',$req->username)->update(['remember_token'=>$token]);
                    $level  = User::where('username',$req->username)->first();
                    session(['username'=>$req->username]);
                    session(['token'=>$token]);
                    session(['level'=>$level->level]);
                    $data = ['body'=>'Success'];
                    return response(json_encode($data),201)->header('Content-Type','text/plain');
                } else {
                    $data = ['body'=>'Password Wrong'];
                    return response(json_encode($data),421)->header('Content-Type','text/plain');
                }
            }
        }
        if ($pt<=0 || $us<=0) {
            $data = ['body'=>'Tidak ada'];
            return response(json_encode($data),401)->header('Content-Type','text/plain');
        }
    }
    public function logout(Request $req){

        $data = User::where('username',session('username'))->first();

        if (session('token')==$data->remember_token) {

            User::where('username',$req->username)->update([
                'remember_token'=>null
            ]);

            session()->forget('username');
            session()->forget('token');
            session()->forget('level');

            return view('authentication.login');

        } else {
            $data = ['body'=>'gagal'];
            return response(json_encode($data),401)->header('Content-Type','text/plain');
        }

    }

}
