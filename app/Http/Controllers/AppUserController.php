<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Validator;
use Timestamp;
use Illuminate\Support\Facades\Log;

class AppUserController extends Controller
{
    //管理者ログイン(get)
    public function loginadmin(){
        return view('appusers.loginadmin');
    }

    //管理者ログイン(post)
    public function loginadmin_p(Request $request){
        //バリデーション処理
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];
        $messages=[
                'email.required' => 'メールアドレスは必ず入力してください。',
                'password.required' => 'パスワードは必ず入力して下さい。',
        ];
        $validator  = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return redirect('/login/admin')
            ->withErrors($validator)
            ->withInput();
        }

        $param = [
            'user_pass' => $request->password,
            'user_email' => $request->email,
        ];
        $items = DB::select('select admin from user where user_pass = :user_pass and user_email = :user_email',$param);

        //ユーザ認証
        //エラー処理
        if(empty($items)||($items[0]->admin != 'admin')){
            $request->session()->flash('login_errors', '管理者アカウントではありません。管理者アカウントで再度ログインしてください。');
            return redirect('/login/admin');
        }else{
            $param = [
                'user_pass' => $request->password,
                'user_email' => $request->email,
            ];
            $items = DB::select('select user_id, user_pass, user_name, user_email, admin from user order by user_id');
            $user = DB::select('select user_id from user where user_pass = :user_pass and user_email = :user_email',$param);
            $request->session()->put('user_id', $user[0]->user_id);
        }   
        //ユーザ一覧画面に遷移
        return view('appusers.usersadmin', ['userdata' => $items]);
    }

    //ユーザ管理画面(post)
    public function useradmin(){
        $items = DB::select('select user_id, user_pass, user_name, user_email, admin from user order by user_id');
        //ユーザ一覧画面に遷移
        return view('appusers.usersadmin', ['userdata' => $items]);
    }

    //ユーザ削除(get)
    public function userdelete(Request $request){
        $param = [
            'user_id' => $request->user_id,
        ];
        DB::delete('delete from user where user_id = :user_id',$param);
        DB::update('update user set user_id = user_id - 1 where user_id > :user_id ',$param);
        $request->session()->flash('delete_message', 'ユーザを削除しました。');
        
        //ユーザ一覧画面に遷移
        return redirect('/administrator');
    }

    //タスク修正(get)
    public function userfix(Request $request){
        $param = [
            'user_id' => $request->user_id,
        ];
        $items = DB::select('select user_id, user_name, user_pass, user_email, admin from user where user_id = :user_id',$param);
        
        //タスク修正画面に遷移
        return view('appusers.userfix',['users'=>$items]);
    }

    //ユーザ修正(post)
    public function userfix_create(Request $request){
        //バリデーション処理
        $rules = [
            'user_name' => 'required',
            'user_pass' => 'required',
            'user_email' => 'required|email',
            'admin' => 'required',
        ];
        $messages=[
                'user_email.required' => 'メールアドレスは必ず入力してください。',
                'user_email.email' => 'メールアドレスは適切な書式で入力してください。',
                'user_pass.required' => 'パスワードは必ず入力して下さい。',
                'user_name.required' => 'ユーザ名は必ず入力して下さい。',
                'admin.required' => '権限は必ず設定して下さい。',
        ];

        //エラー処理
        $request->session()->flash('user_errors', '入力項目に問題があります。');
        $validator  = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return redirect('user/fix/'.$request->user_id)
            ->withErrors($validator)
            ->withInput();
        }

        //更新処理
        $param = [
            'user_id' => $request->user_id,
            'user_name' => $request->user_name,
            'user_pass' => $request->user_pass,
            'user_email' => $request->user_email,
            'admin' => $request->admin,
        ];
        DB::update('update user set user_name = :user_name, user_pass = :user_pass, user_email = :user_email, admin = :admin where user_id = :user_id',$param);
        $request->session()->flash('update_message', 'ユーザを更新しました');
        
        //ユーザ一覧に画面遷移する
        return redirect('/administrator');
    }


}
