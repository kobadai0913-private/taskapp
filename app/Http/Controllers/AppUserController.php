<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        //ユーザ認証
        $user_admin = DB::table('user')
                    ->select('admin')
                    ->where('user_pass','=',$request->password)
                    ->where('user_email','=',$request->email)
                    ->get();

        //エラー処理
        if($user_admin == ''||($user_admin != 'admin')){
            $request->session()->flash('login_errors', '管理者アカウントではありません。管理者アカウントで再度ログインしてください。');
            return redirect('/login/admin');
        }
        
         //ユーザ一覧画面遷移
         return self::user_admin_list($request);
    }

    //ユーザ管理画面(get)
    public function user_admin_list(Request $request){
        $request->session()->put('admin_flg',true);
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

    //ユーザ削除
    public function user_delete(Request $request){
        $user_name  = DB::table('user')
                        ->select('user_name')
                        ->where('user_id','=',$request->user_id)
                        ->get();
        foreach($user_name as $name){
            $user_name = $name->user_name;
        }
        //タスクがあるかチェック
        $task_id  = DB::table('user_taskmanage')
                        ->select('task_id')
                        ->where('user_id','=',$request->user_id)
                        ->first();
        if($task_id!=''){
            $request->session()->flash('userdeleteerror_message', '当該ユーザのタスクが残っているため削除できませんでした。');
        }else{
            $user_datas = DB::table('user')
                            ->select('user_id')
                            ->where('user_id','>',$request->user_id)
                            ->orderby('user_id','asc')
                            ->get();
            $param = [
                'user_id' => $request->user_id,
            ];   
            DB::delete('delete from user where user_id = :user_id',$param);
            foreach($user_datas as $user_id){
                $param = [
                    'user_id' => $user_id->user_id,
                    'user_id_count' => $user_id->user_id,
                ];
                DB::update('update user set user_id = :user_id_count - 1 where user_id = :user_id ',$param);
            }
            $alter_sql = 'alter table information_board drop column '.str($user_name).'_flg';
            DB::statement($alter_sql);
            DB::rollback();
            DB::commit();
            $request->session()->flash('delete_message', 'ユーザを削除しました。');
        }
        
        //ユーザ一覧画面に遷移
        return redirect('/administrator');
    }

    //ユーザ修正(get)
    public function user_fix(Request $request){
        $param = [
            'user_id' => $request->user_id,
        ];
        $items = DB::select('select user_id, user_name, user_pass, user_email, admin from user where user_id = :user_id',$param);
        
        //タスク修正画面に遷移
        return view('appusers.user_fix',['users'=>$items, 'user_id'=>$request->user_id]);
    }

    //ユーザ修正(post)
    public function user_fix_registration(Request $request){
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
    public function user_login(Request $request){
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
