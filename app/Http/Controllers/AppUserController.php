<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Validator;
use Timestamp;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TaskController;

class AppUserController extends Controller
{
    //管理者ログイン(get)
    public function login_admin(){
        return view('appusers.login_admin');
    }

    //管理者ログイン(post)
    public function login_admin_registration(Request $request){
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
        }
        
        //ユーザ一覧画面遷移
        return self::user_admin_list($request);
    }

    //ユーザ管理画面(get)
    public function user_admin_list(Request $request){
        $param = [
            "admin" => "admin",
        ];
        $user = DB::select('select user_id, admin from user where admin = :admin',$param);

        $items = DB::select('select user_id, user_pass, user_name, user_email, admin from user order by user_id');
        $request->session()->put('user_id', $user[0]->user_id);
        $request->session()->put('admin', $user[0]->admin);
        
        //ユーザ一覧画面に遷移
        return view('appusers.user_admin', ['userdata' => $items]);
    }

    //ユーザ削除(get)
    public function user_delete(Request $request){
        $login_userid = $request->session()->get('user_id');
        $param = [
            'user_id' => $request->user_id,
        ];

        //タスクがあるかチェック
        $items = DB::select('select task_id from user_taskmanage where user_id = :user_id', $param);
        if(!empty($items)){
            $request->session()->flash('userdeleteerror_message', '当該ユーザのタスクが残っているため削除できませんでした。');
        }else{
            DB::delete('delete from user where user_id = :user_id',$param);
            DB::update('update user set user_id = user_id - 1 where user_id > :user_id ',$param);
            $request->session()->flash('delete_message', 'ユーザを削除しました。');
            $request->session()->put('user_id', $login_userid);
        }
        
        //ユーザ一覧画面に遷移
        return redirect('/administrator');
    }

    //タスク修正(get)
    public function user_fix(Request $request){
        $param = [
            'user_id' => $request->user_id,
        ];
        $items = DB::select('select user_id, user_name, user_pass, user_email, admin from user where user_id = :user_id',$param);
        
        //タスク修正画面に遷移
        return view('appusers.user_fix',['users'=>$items, 'user_id'=>$request->user_id]);
    }

    //ユーザ修正(post)
    public function user_fix__registration(Request $request){
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

    //管理者ユーザログイン(get)
    public function userlogin(Request $request){
        $user_id = $request->user_id;

        $param=[
            'user_id' => $user_id,
        ];
        $items = DB::select('select user_id, admin from user where user_id = :user_id',$param);

        foreach($items as $it){
            $user_id = $it->user_id;
        }
        $request->session()->put('user_id', $user_id);

        //タスク一覧画面遷移
        $task_controller = new TaskController;
        return $task_controller->taskapp_list($request);
    }
}
