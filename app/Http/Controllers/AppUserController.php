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

use App\Model\User;

class AppUserController extends Controller
{
    //管理者ログイン(get)
    public function login_admin(){
        return view('appusers.login_admin');
    }

    //管理者ログイン(post)
    public function login_admin_registration(Request $request){
        //入力情報取得
        $user_pass = $request->user_pass;
        $user_email = $request->user_email;

        //変数定義
        $user_admin;
        
        //バリデーション情報取得
        $rules = User::$rules;
        $messages = User::$messages;
        unset($rules['user_name']);
        unset($messages['user_name.required']);

        //バリデーション処理
        $validator  = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return redirect('/login/admin')
            ->withErrors($validator)
            ->withInput();
        }

        //ユーザ権限処理
        $user_admin = User::select('admin')
                    ->where('user_pass','=',$user_pass)
                    ->where('user_email','=',$user_email)
                    ->first();
        if($user_admin == null||($user_admin->admin != 'admin')){
            $request->session()->flash('login_errors', '管理者アカウントではありません。管理者アカウントで再度ログインしてください。');
            return redirect('/login/admin');
        }

        //ユーザ権限取得
        $user_admin = $user_admin->admin;
        
         //ユーザ一覧画面遷移
         return self::user_admin_list($request);
    }

    //ユーザ管理画面(get)
    public function user_admin_list(Request $request){

        //管理者権限フラグセット
        $request->session()->put('admin_flg',true);

        //変数定義
        $user_datas;

        //ユーザID、ユーザ権限取得
        $user = User::select('user_id','admin')
                        ->where('admin','admin')
                        ->get();

        $user_datas = User::select('user_id','user_pass','user_name','user_email','admin')
                                ->orderby('user_id','asc')
                                ->get();

        //セッション情報登録
        $request->session()->put('user_id', $user[0]->user_id);
        $request->session()->put('admin', $user[0]->admin);
        
        //ユーザ一覧画面に遷移
        return view('appusers.user_admin', ['userdata' => $user_datas]);
    }

    //ユーザ削除
    public function user_delete(Request $request){
        //ユーザID取得
        $user_id = $request->user_id;

        //変数定義
        $user_name;
        $user_taskid;

        //ユーザ名取得
        $user_data = User::select('user_name')
                            ->where('user_id',$user_id)
                            ->get();
        $user_name = $user_data[0]->user_name;

        //タスクがあるかチェック
        $user_taskid = Task::select('task_id')
                                ->where('user_id','=',$user_id)
                                ->first()
                                ->get();
        if($user_taskid!=''){
            $request->session()->flash('userdeleteerror_message', '当該ユーザのタスクが残っているため削除できませんでした。');
        }else{
            $user_datas = DB::select('user_id')
                            ->where('user_id','>',$user_id)
                            ->orderby('user_id','asc')
                            ->get();  
            
            //ユーザデータ削除
            User::where('user_id',$user_id)
                    ->delete();
            
            foreach($user_datas as $user_data){
                $get_userid = $user_data->user_id;

                //ユーザID更新処理
                $update_param = [
                    'user_id' => ($get_userid-1),
                ];
                User::where('user_id',$get_userid)
                        ->update($update_param);
            }

            //ユーザカラム削除処理
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

        //ユーザID取得
        $user_id = $request->user_id;

        //ユーザ情報取得
        $items = User::select('user_id','user_name','user_pass','user_email','admin')
                            ->where('user_id',$user_id)
                            ->get();

        //タスク修正画面に遷移
        return view('appusers.user_fix',['users'=>$items, 'user_id'=>$request->user_id]);
    }

    //ユーザ修正(post)
    public function user_fix_registration(Request $request){
        //入力情報取得
        $user_id = $request->user_id;
        $user_name = $request->user_name;
        $user_pass = $request->user_pass;
        $user_email = $request->user_email;

        //追加部分
        $user_admin = $request->authority;

        //ユーザID取得
        $user_id = $request->user_id;

        //変数
        $insert_admin;
        
        //バリデーション情報取得
        $rules = User::$rules;
        $messages = User::$messages;

        //バリデーション処理
        $request->session()->flash('user_errors', '入力項目に問題があります。');
        $validator  = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return redirect('user/fix/'.$user_id)
            ->withErrors($validator)
            ->withInput();
        }

        //追加部分
        //権限設定
        if($user_admin == 'user_authority'){
            $insert_admin = "user";
        }else{
            $insert_admin = "admin";
        }

        //updateパラメータ取得
        $update_param = User::$user_param;

        //updateパラメータセット
        unset($update_param['user_id']);
        $update_param['user_name']=$user_name;
        $update_param['user_pass']=$user_pass;
        $update_param['user_email']=$user_email;

        //追加部分
        $update_param['admin']=$insert_admin;
        
        User::where('user_id',$user_id)->update($update_param);

        $request->session()->flash('update_message', 'ユーザを更新しました');
        
        //ユーザ一覧に画面遷移する
        return redirect('/administrator');
    }

    //管理者ユーザログイン(get)
    public function user_login(Request $request){

        //ユーザID取得
        $user_id = $request->user_id;

        //セッション情報登録
        $request->session()->put('user_id', $user_id);

        //タスク一覧画面遷移
        $task_controller = new TaskController;
        return $task_controller->taskapp_list($request);
    }
}
