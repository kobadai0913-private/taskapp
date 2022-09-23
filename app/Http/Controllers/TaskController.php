<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Log;

use App\Model\Task;
use App\Model\User;
use App\Model\Information;

class TaskController extends Controller
{
    //タスク修正(get)
    public function task_fix(Request $request){

        //入力内容取得
        $task_id = $request->task_id;

        //タスクデータ取得
        $items = Task::select('task_id','task_name','task_detail',Task::raw('date_format(task_start_datetime,"%Y-%m-%dT%k:%i") as task_start_datetime'),Task::raw('date_format(task_end_datetime,"%Y-%m-%dT%k:%i") as task_end_datetime'),'completed')
                            ->where('task_id',$task_id)
                            ->get();

        //タスク修正画面に遷移
        return view('task.task_fix',['tasks'=>$items]);
    }

    //タスク修正(post)
    public function task_fix_registration(Request $request){

         //入力内容取得
         $task_id = $request->task_id;
         $task_name = $request->task_name;
         $task_detail = $request->task_detail;
         $task_start_datetime = $request->task_start_datetime;
         $task_end_datetime = $request->task_end_datetime;
         $task_start_datetime_status = $request->task_start_datetime_status;
 
         //作業用変数
         $work_start_datetime;
         $work_start_date;
         $work_end_datetime;
         $work_end_date;
         $work_id;
         $work_column;
         $work_max_id;
         $now;
         $now_date;
         $now_datetime;
         $completed;
 
         //バリデーション情報取得
         $rules = Task::$rules;
         $messages = Task::$messages;
 
         //insertパラメータ取得
         $insert_param = Task::$task_param;

        //タスクパラメータ削除処理
        if($task_start_datetime_status=='true'){
            unset($rules['task_start_datetime']);
            unset($messages['task_start_datetime.required']);
        }

        //エラー処理
        $validator  = Validator::make($request->all(), $rules, $messages);
        $request->session()->flash('task_errors', '入力項目に問題があります。');
        if($validator->fails()){
            return redirect('/task/fix/'.$task_id)
            ->withErrors($validator)
            ->withInput();
        }

        //追加タスクパラメータセット処理
        if($task_start_datetime_status=='true'){
             //insertパラメータセット
             unset($insert_param['user_id']);
             $insert_param['task_id']=$task_id;
             $insert_param['task_name']=$task_name;
             $insert_param['task_detail']=$task_detail;
             $insert_param['task_start_datetime']=$task_start_datetime;
             $insert_param['task_end_datetime']=$task_end_datetime;
             $insert_param['completed']='deadline_incomplete';
        }else{
            //日付・時間処理
            //タスク開始日付・時間
            $work_start_datetime = Carbon::parse($task_start_datetime);
            $work_start_date = $work_start_datetime->format("Y/m/d");
            $work_start_datetime = $work_start_datetime->format('Y-m-d H:i:s');
            //タスク終了日付・時間
            $work_end_datetime = Carbon::parse($task_end_datetime);
            $work_end_date = $work_end_datetime->format("Y/m/d");
            $work_end_datetime = $work_end_datetime->format('Y-m-d H:i:s');
            //今日の日付・時間
            $now = Carbon::now();
            $now_date = $now->format("Y/m/d");
            $now_datetime = $now->format('Y-m-d H:i:s');

            //フラグ設定処理
            if($work_end_datetime<$now_datetime){
                $completed = "excess_incomplete";   
            }elseif(($work_start_date<=$now_date)&&($work_end_date>=$now_date)){
                $completed = "today_incomplete";
            }else{
                $completed = "future_incomplete";
            }
            unset($insert_param['user_id']);
            unset($insert_param['task_id']);
            $insert_param['task_name']=$task_name;
            $insert_param['task_detail']=$task_detail;
            $insert_param['task_start_datetime']=$task_start_datetime;
            $insert_param['task_end_datetime']=$task_end_datetime;
            $insert_param['completed']=$completed;
        }

        //タスク更新処理
        Task::where('task_id',$task_id)->update($insert_param);
        $request->session()->flash('update_message', 'タスクを更新しました');
        
        //タスク一覧に画面遷移する
        return redirect('task/app');
    }

    //タスク追加(get)
    public function task_insert(){
        //タスク追加画面に遷移
        return view('task.task_insert');
    }

    //タスク追加(post)
    public function task_insert_registration(Request $request){
        //セッション情報取得
        $user_id = $request->session()->get('user_id');

        //入力内容取得
        $task_id;
        $task_name = $request->task_name;
        $task_detail = $request->task_detail;
        $task_start_datetime = $request->task_start_datetime;
        $task_end_datetime = $request->task_end_datetime;
        $task_start_datetime_status = $request->task_start_datetime_status;
        $task_start_datetime_counter = $request->task_start_datetime_counter;
        $task_end_datetime_counter = $request->task_end_datetime_counter;

        //作業用変数
        $work_start_datetime;
        $work_start_date;
        $work_end_datetime;
        $work_end_date;
        $work_column;
        $work_max_id;
        $now;
        $now_date;
        $now_datetime;
        $completed;

        //バリデーション情報取得
        $rules = Task::$rules;
        $messages = Task::$messages;

        //insertパラメータ取得
        $insert_param = Task::$task_param;

        //追加タスク件数
        $insert_count = 1;

        //タスクパラメータ削除処理
        if($task_start_datetime_status=='true'){
            unset($rules['task_start_datetime']);
            unset($messages['task_start_datetime.required']);
            unset($messages['task_start_datetime.task_datetime']);
        }       
        if($task_start_datetime_counter!=''){
            for($i=0; $i<$task_start_datetime_counter; $i++){
                $work_column = "task_start_datetime".($i+1);
                $rules = $rules+[$work_column => 'required|task_datetime'];
                $messages = $messages+[$work_column.'.required' => 'タスク開始日付・時間は必ず入力して下さい。'];
                $messages = $messages+[$work_column.'.task_datetime' => 'タスク開始日付には過去の日付を登録することはできません。'];
                $request->session()->put('task_start_datetime_counter',$task_start_datetime_counter);
            }
        }
        if($task_end_datetime_counter!=''){
            for($i=0; $i<$task_end_datetime_counter; $i++){
                $work_column = "task_end_datetime".($i+1);
                $rules = $rules+[$work_column => 'required|after_or_equal:task_start_datetime'.($i+1)];
                $messages = $messages+[$work_column.'.required' => 'タスク開始日付・時間は必ず入力して下さい。'];
                $messages = $messages+[$work_column.'.after_or_equal' => 'タスク終了日付にはタスク開始日付・時間以降の日付を入力して下さい。'];
                $request->session()->put('task_end_datetime_counter',$task_end_datetime_counter);
            }
        }

        //エラー処理
        $validator  = Validator::make($request->all(), $rules, $messages);
        $request->session()->flash('task_errors', '入力項目に問題があります。');
        if($validator->fails()){
            return redirect('task/add')
            ->withErrors($validator)
            ->withInput();
        }

        //タスク追加処理
        //タスクID作成
        $work_max_id = Task::select('task_id')
                            ->orderBy('task_id','desc')
                            ->first();
        if($work_max_id==null){
            $task_id = 1;
        }else{
            $task_id = $work_max_id->task_id+1;
        }

        //追加タスクパラメータセット処理
        if($task_start_datetime_status=='true'){
            //insertパラメータセット
            $insert_param['user_id']=$user_id;
            $insert_param['task_id']=$task_id;
            $insert_param['task_name']=$task_name;
            $insert_param['task_detail']=$task_detail;
            $insert_param['task_start_datetime']=$task_start_datetime;
            $insert_param['task_end_datetime']=$task_end_datetime;
            $insert_param['completed']='deadline_incomplete';
        }else{
            //日付・時間処理
            //タスク開始日付・時間
            $work_start_datetime = Carbon::parse($task_start_datetime);
            $work_start_date = $work_start_datetime->format("Y/m/d");
            $work_start_datetime = $work_start_datetime->format('Y-m-d H:i:s');
            //タスク終了日付・時間
            $work_end_datetime = Carbon::parse($task_end_datetime);
            $work_end_date = $work_end_datetime->format("Y/m/d");
            $work_end_datetime = $work_end_datetime->format('Y-m-d H:i:s');
            //今日の日付・時間
            $now = Carbon::now();
            $now_date = $now->format("Y/m/d");
            $now_datetime = $now->format('Y-m-d H:i:s');

            //フラグ設定処理
            if($work_end_datetime<$now_datetime){
                $completed = "excess_incomplete";   
            }elseif(($work_start_date<=$now_date)&&($work_end_date>=$now_date)){
                $completed = "today_incomplete";
            }else{
                $completed = "future_incomplete";
            }
            //insertパラメータセット
            $insert_param['user_id']=$user_id;
            $insert_param['task_id']=$task_id;
            $insert_param['task_name']=$task_name;
            $insert_param['task_detail']=$task_detail;
            $insert_param['task_start_datetime']=$work_start_datetime;
            $insert_param['task_end_datetime']=$work_end_datetime;
            $insert_param['completed']=$completed;
        }

        //初期タスク追加処理
        Task::insert($insert_param);

        //追加タスク処理
        if($task_start_datetime_counter!=''){
            //入力内容取得
            $task_start_datetime1 = $request->task_start_datetime1;
            $task_end_datetime1 = $request->task_end_datetime1;
            $task_start_datetime2 = $request->task_start_datetime2;
            $task_end_datetime2 = $request->task_end_datetime2;
            $task_start_datetime3 = $request->task_start_datetime3;
            $task_end_datetime3 = $request->task_end_datetime3;
            $task_start_datetime4 = $request->task_start_datetime4;
            $task_end_datetime4 = $request->task_end_datetime4;
            $task_start_datetime5 = $request->task_start_datetime5;
            $task_end_datetime5 = $request->task_end_datetime5;

            for($i=0; $i<$task_start_datetime_counter; $i++){
                //タスク追加処理
                if($i==0){
                    $work_start_datetime = $task_start_datetime1;
                    $work_end_datetime = $task_end_datetime1;
                }elseif($i==1){
                    $work_start_datetime = $task_start_datetime2;
                    $work_end_datetime = $task_end_datetime2;
                }elseif($i==2){
                    $work_start_datetime = $task_start_datetime3;
                    $task_end_datetime = $task_end_datetime3;
                }elseif($i==3){
                    $work_start_datetime = $task_start_datetime4;
                    $work_end_datetime = $task_end_datetime4;
                }elseif($i==4){
                    $work_start_datetime = $task_start_datetime5;
                    $work_end_datetime = $task_end_datetime5;
                }

                //日付・時間処理
                //タスク開始日付・時間
                $work_start_datetime = Carbon::parse($work_start_datetime);
                $work_start_date = $work_start_datetime->format("Y/m/d");
                $work_start_datetime = $work_start_datetime->format('Y-m-d H:i:s');
                //タスク終了日付・時間
                $work_end_datetime = Carbon::parse($work_end_datetime);
                $work_end_date = $work_end_datetime->format("Y/m/d");
                $work_end_datetime = $work_end_datetime->format('Y-m-d H:i:s');
                //今日の日付・時間
                $now = Carbon::now();
                $now_date = $now->format("Y/m/d");
                $now_datetime = $now->format('Y-m-d H:i:s');

                //フラグ設定処理
                if($work_end_datetime<$now_datetime){
                    $completed = "excess_incomplete";   
                }elseif(($work_start_date<=$now_date)&&($work_end_date>=$now_date)){
                    $completed = "today_incomplete";
                }else{
                    $completed = "future_incomplete";
                }

                //タスクID作成処理
                $work_max_id = Task::select('task_id')
                            ->orderBy('task_id','desc')
                            ->first();
                if($work_max_id==null){
                    $task_id = 1;
                }else{
                    $task_id = $work_max_id->task_id+1;
                }

                //insertパラメータセット
                $insert_param['user_id']=$user_id;
                $insert_param['task_id']=$task_id;
                $insert_param['task_name']=$task_name;
                $insert_param['task_detail']=$task_detail;
                $insert_param['task_start_datetime']=$work_start_datetime;
                $insert_param['task_end_datetime']=$work_end_datetime;
                $insert_param['completed']=$completed;

                //追加タスク追加
                Task::insert($insert_param);
                $insert_count += 1;
            }
        }
        
        //タスク追加メッセージ
        $request->session()->flash('insert_message', str($insert_count).'件のタスクを追加しました。');

        //タスク一覧画面に遷移
        return redirect('/task/app');
    }

    //タスク一覧(get)
    public function taskapp_list(Request $request){

        //タスク日付カウンター削除
        $request->session()->forget('task_start_datetime_counter');
        $request->session()->forget('task_end_datetime_counter');

        //変数定義
        $users_data;
        $user_admin;
        $user_name;
        $information_datas;
        $covid_data;
        $now_date;

        //ユーザID取得
        $user_id = $request->session()->get('user_id');

        //ユーザ権限確認
        $users_data = User::select('admin','user_name')
                            ->where('user_id',$user_id)
                            ->get();
        foreach($users_data as $user_data){
            $user_admin = $user_data->admin;
            $user_name = $user_data->user_name;
        }

        //権限付与
        $request->session()->put('admin',$user_admin);

        //タスク権限更新処理
        self::usertask_date_update($user_id);

        //タスクデータ取得
        if($user_admin == "admin"){
            $items = Task::select('task_id','task_name','task_detail',Task::raw('date_format(task_start_datetime,"%Y年%m月%d日 %k時%i分") as task_start_datetime'),Task::raw('date_format(task_end_datetime,"%Y年%m月%d日 %k時%i分") as task_end_datetime'),'user_id','completed')
                            ->orderby('user_id','asc')
                            ->get();
        }else{
            $items = Task::select('task_id','task_name','task_detail',Task::raw('date_format(task_start_datetime,"%Y年%m月%d日 %k時%i分") as task_start_datetime'),Task::raw('date_format(task_end_datetime,"%Y年%m月%d日 %k時%i分") as task_end_datetime'),'user_id','completed')
                            ->where('user_id',$user_id)
                            ->get();
        }

        //インフォメーション情報取得
        $information_datas = Information::select('information_id','information_name','information_detail','information_date',str($user_name).'_flg')
                                            ->orderby('information_id','desc')
                                            ->get();
        
        //コロナapi呼び出し
        $covid_data = self::covid19();

        //現在時間取得
        $now_date = date("Y年m月d日 H時i分s秒"); 

        //タスク一覧画面に遷移
        return view('task.taskapp_top',['tasks'=>$items, 'api'=>$covid_data, 'date'=>$now_date, 'informations'=>$information_datas, 'user_name'=>$user_name]);
    }

    //タスク詳細(get)
    public function task_detail(Request $request){
        //タスクID取得
        $task_id = $request->task_id;

        //タスク情報取得
        $items = Task::select('task_id','task_name','task_detail',Task::raw('date_format(task_start_datetime,"%Y年%m月%d日 %k時%i分") as task_start_datetime'),Task::raw('date_format(task_end_datetime,"%Y年%m月%d日 %k時%i分") as task_end_datetime'),'completed')
                            ->where('task_id',$task_id)
                            ->get();

        //タスク詳細画面に遷移
        return view('task.task_detail',['tasks'=>$items]);
    }

    //タスク削除(get)
    public function task_delete(Request $request){
        //タスクID取得
        $task_id = $request->task_id;

        //変数定義
        $task_datas;

        //指定したタスクID以上のタスクIDを昇順抽出
        $task_datas = Task::select('task_id')
                            ->where('task_id','>',$task_id)
                            ->orderby('task_id','asc')
                            ->get();

        //タスクデータ削除
        Task::where('task_id',$task_id)
                ->delete();

        foreach($task_datas as $task_data){
            //タスクID取得
            $get_task_id = $task_data->task_id;

            //タスクID更新
            $update_param=[
                'task_id'=>($get_task_id-1),
            ];
            Task::where('task_id',$get_task_id)->update($update_param);
        }

        $request->session()->flash('delete_message', 'タスクを削除しました。');
        
        //タスク一覧画面に遷移
        return redirect('/task/app');
    }

    //ログイン(get)
    public function task_login(){
        //ログイン画面に遷移
        return view('task.taskapp_login');
    }

    //ログイン(post)
    public function task_login_registration(Request $request){
        //セッション削除
        $request->session()->flush();

        //変数
        $user_admin;
        $user_id;

        //入力情報取得
        $user_pass = $request->user_password;
        $user_email = $request->user_email;

        //バリデーション情報取得
        $rules = User::$rules;
        $messages = User::$messages;

        //バリデーション情報編集
        unset($rules['user_name']);
        unset($messages['user_name.required']);

        //バリデーション処理
        $validator  = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return redirect('/task')
            ->withErrors($validator)
            ->withInput();
        }

        //ユーザ認証
        $user = User::select('user_id','admin')
                        ->where('user_email',$user_email)
                        ->where('user_pass',$user_pass)
                        ->get();
        $user_id = $user[0]->user_id;
        $user_admin = $user[0]->admin;

        //エラー処理
        if(empty($items)){
            $request->session()->flash('login_errors', '入力項目に問題があります。');
            return redirect('/task');
        }elseif($user_admin == "admin"){
            $request->session()->flash('login_errors', 'こちらでは管理者ログインを行うことができません。');
            return redirect('/task');
        }

        //セッション登録
        $request->session()->put('user_id', $user_id);
        $request->session()->put('admin', $user_admin);
        $request->session()->put('admin_flg', false);
       
        //タスク一覧画面遷移
        return self::taskapp_list($request);
    }

    //新規会員登録(get)
    public function new_member(){
        return view('task.loginuser_insert');
    }

    //新規会員登録(post)
    public function new_member_registration(Request $request){
        //入力情報取得
        $user_name = $request->user_name;
        $user_pass = $request->user_password;
        $user_email = $request->user_email;
        $user_admin = $request->authority;

        //変数
        $user_id;
        $get_user_id;
        $insert_admin;

        //バリデーション情報取得
        $rules = User::$rules;
        $messages = User::$messages;

        //バリデーション処理
        $validator  = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return redirect('/login/insert')
            ->withErrors($validator)
            ->withInput();
        }
        
        $get_user_id = User::select('user_id')
                                ->orderBy('user_id','desc')
                                ->first()
                                ->get();
            
        if($work_user_id==null){
            $user_id = 1;
        }else{
            $user_id = $get_user_id+1;
        }

        //権限設定
        if($user_admin == 'user_authority'){
            $insert_admin = "user";
        }else{
            $insert_admin = "admin";
        }

        //insertパラメータ取得
        $insert_param = User::$users_param;

        //insertパラメータセット
        $insert_param['user_id']=$user_id;
        $insert_param['user_name']=$user_name;
        $insert_param['user_password']=$user_pass;
        $insert_param['user_email']=$user_email;
        $insert_param['admin']=$insert_admin;
        User::insert($insert_param);

        //ユーザ名フラグ追加
        $alter_sql = 'alter table information_board add column '.str($user_name).'_flg boolean default false';
        DB::statement($alter_sql);

        $request->session()->flash('insert_message', 'ユーザを追加しました。');
        
        //ログイン画面に遷移
        return redirect('/task');
    }

    //csv出力
    public function task_csv(Request $request)
    {
        //ユーザID取得
        $user_id = $request->session()->get('user_id');

        //定数定義
        $counter = 1;
        $completed = '';

        //タスクデータ取得
        $items = Task::select('task_id','task_name','task_detail',Task::raw('date_format(task_start_datetime,"%Y-%m-%dT%k:%i") as task_start_datetime'),Task::raw('date_format(task_end_datetime,"%Y-%m-%dT%k:%i") as task_end_datetime'),'completed')
                            ->where('user_id',$user_id)
                            ->get();

        //タスク配列作成
        $data = [];
        $data[] = ['タスク一覧'];
        $data[] = ['No', 'タスク名', 'タスク詳細', 'タスク日付', 'タスク時間','ステータス'];
        foreach($items as $item){
            if($item->completed=='excess_incomplete'){
                $completed = "未完了のタスク";
            }elseif($item->completed=='today_incomplete'){
                $completed = "今日のタスク";
            }elseif($item->completed=='future_incomplete'){
                $completed = "明日以降のタスク";
            }else{
                $completed = "期限タスク";
            }
            $data[] = [$counter,$item->task_name,$item->task_detail,$item->task_start_datetime,$item->task_end_datetime,$completed];
            $counter += 1;
        }
        
        //csvファイル作成
        $save_file = storage_path('csv/task.csv');
        $file = new \SplFileObject($save_file, 'w'); // ファイルが無ければ作成
        $file->setCsvControl(",");                   // カンマ区切り
        foreach ($data as $row) {
            mb_convert_variables('SJIS', 'UTF-8', $row);
            $file->fputcsv($row);
        }

        return response()->download(storage_path('csv/task.csv'));
    }

    //タスク完了更新処理
    public function task_success_update(Request $request){

        //タスクID取得
        $task_id = $request->task_id;

        //タスク権限更新
        $update_param = [
            'completed' => 'complete',
        ];
        Task::where('task_id')
                ->update($update_param);

        $request->session()->flash('completed_message', 'タスクを完了済みに更新しました。');
        
        //タスク一覧画面に遷移
        return redirect('/task/detail/'.$request->task_id);
    }

    //タスク完了取消処理
    public function task_success_denger(Request $request){

        //タスクID取得
        $task_id = $request->task_id;

        //変数定義
        $task_data;
        $task_start_datetime;
        $task_start_date;
        $task_end_datetime;
        $task_end_date;
        $now_datetime;
        $now_date;
        $now;

        //初期値設定
        $comoleted = '';

        //タスク開始日付・終了日付取得
        $task_data = Task::select('task_start_datetime','task_end_datetime')
                ->where('task_id',$task_id)
                ->get();
        $task_start_datetime = $task_data[0]->task_start_datetime;
        $task_end_datetime = $task_data[0]->task_end_datetime;
         
        if($task_start_datetime == null){
            //日付・時間処理
            //タスク終了日付・時間
            $task_end_datetime = Carbon::parse($task_end_datetime);
            $task_end_date = $task_end_datetime->format("Y/m/d");
            $task_end_datetime = $task_end_datetime->format('Y-m-d H:i:s');
            //今日の日付・時間
            $now = Carbon::now();
            $now_date = $now->format("Y/m/d");
            $now_datetime = $now->format('Y-m-d H:i:s');

            //フラグ設定処理
            if($task_end_datetime<$now_datetime){
                $completed = "excess_incomplete";   
            }elseif(($task_end_date==$now_date)){
                $completed = "today_incomplete";
            }else{
                $completed = "deadline_incomplete";
            }
        }else{
            //日付・時間処理
            //タスク開始日付・時間
            $task_start_datetime = Carbon::parse($task_start_datetime);
            $task_start_date = $task_start_datetime->format("Y/m/d");
            $task_start_datetime = $task_start_datetime->format('Y-m-d H:i:s');
            //タスク終了日付・時間
            $task_end_datetime = Carbon::parse($task_end_datetime);
            $task_end_date = $task_end_datetime->format("Y/m/d");
            $task_end_datetime = $task_end_datetime->format('Y-m-d H:i:s');
            //今日の日付・時間
            $now = Carbon::now();
            $now_date = $now->format("Y/m/d");
            $now_datetime = $now->format('Y-m-d H:i:s');

            //フラグ設定処理
            if($task_end_datetime<$now_datetime){
                $completed = "excess_incomplete";   
            }elseif(($task_start_date<=$now_date)&&($task_end_date>=$now_date)){
                $completed = "today_incomplete";
            }else{
                $completed = "future_incomplete";
            }
        }

        //タスクフラグ更新処理
        $update_param = [
            'completed' => $completed,
        ];
        Task::where('task_id',$task_id)
                ->update($update_param);

        $request->session()->flash('incomplete_message', 'タスクの完了を取り消しました');
        
        //タスク一覧画面に遷移
        return redirect('/task/detail/'.$task_id);
    }

    //タスク日付更新処理(user)
    public function usertask_date_update($user_id){

        //初期値設定
        $completed = '';

        //変数定義
        $task_start_datetime;
        $task_start_date;
        $task_end_datetime;
        $task_end_date;
        $now_datetime;
        $now_date;
        $now;

        //通常タスク取得
        $task_datas = Task::select('task_id','task_start_datetime','task_end_datetime')
                        ->where('completed','complete')
                        ->where('deadline_completed','deadline_complete')
                        ->where('user_id',$user_id)
                        ->get();

        foreach($task_datas as $task_data){
            $task_id = $task_data->task_id;

            //更新処理
            $task_start_datetime = $select->task_start_datetime;
            $task_end_datetime = $select->task_end_datetime;

            //日付・時間処理
            //タスク開始日付・時間
            $task_start_datetime = Carbon::parse($task_start_datetime);
            $task_start_date = $task_start_datetime->format("Y/m/d");
            $task_start_datetime = $task_start_datetime->format('Y-m-d H:i:s');
            //タスク終了日付・時間
            $task_end_datetime = Carbon::parse($task_end_datetime);
            $task_end_date = $task_end_datetime->format("Y/m/d");
            $task_end_datetime = $task_end_datetime->format('Y-m-d H:i:s');
            //今日の日付・時間
            $now = Carbon::now();
            $now_date = $now->format("Y/m/d");
            $now_datetime = $now->format('Y-m-d H:i:s');

            //フラグ設定処理
            if($task_end_datetime<$now_datetime){
                $completed = "excess_incomplete";   
            }elseif(($task_start_date<=$now_date)&&($task_end_date>=$now_date)){
                $completed = "today_incomplete";
            }else{
                $completed = "future_incomplete";
            }

            //タスク権限更新処理
            $update_param = [
                'completed' => $completed,
            ];
            Task::where('task_id',$task_id)
                ->update($update_param); 
        }

        //期限タスク更新処理
        $task_datas = Task::select('task_id','task_end_datetime')   
                            ->where('user_id',$user_id) 
                            ->where('completed','deadline_incomplete')
                            ->get();

        if(!empty($task_datas)){
            foreach($task_datas as $task_data){
                $task_id = $task_data->task_id;
    
                //更新処理
                $task_end_datetime = $select->task_end_datetime;
    
                //日付・時間処理
                //タスク終了日付・時間
                $task_end_datetime = Carbon::parse($task_end_datetime);
                $task_end_date = $task_end_datetime->format("Y/m/d");
                $task_end_datetime = $task_end_datetime->format('Y-m-d H:i:s');
                //今日の日付・時間
                $now = Carbon::now();
                $now_date = $now->format("Y/m/d");
                $now_datetime = $now->format('Y-m-d H:i:s');
    
                //フラグ設定処理
                if($task_end_datetime<$now_datetime){
                    $completed = "excess_incomplete";   
                }elseif(($task_end_date==$now_date)){
                    $completed = "today_incomplete";
                }else{
                    $completed = "deadline_incomplete";
                }

                //タスクフラグ更新処理
                $update_param = [
                    'completed' => $completed,
                ];
                Task::where('task_id',$task_id)
                    ->update($update_param);
            }
        }      
    }

    //コロナ情報取得
    public function covid19(){
        // APIアクセスURL
        $url = 'https://covid19-japan-web-api.now.sh/api/v1/prefectures';
        // ストリームコンテキストのオプションを作成
        $options = array(
            // HTTPコンテキストオプションをセット
            'http' => array(
                'method'=> 'GET',
                'header'=> 'Content-type: application/json; charset=UTF-8' //JSON形式で表示
            )
        );
        // ストリームコンテキストの作成
        $context = stream_context_create($options);
        $raw_data = file_get_contents($url, false,$context);
        $result = json_decode($raw_data, true);
        $data = [$result[0]['cases'],$result[0]['deaths'],$result[0]['pcr'],$result[0]['hospitalize'],$result[0]['discharge']];
        return $data;
    }
}

