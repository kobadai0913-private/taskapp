<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Validator;
use Timestamp;
use DateTime;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    //タスク修正(get)
    public function task_fix(Request $request){

        $param = [
            "task_id" => $request->task_id,
            "time" => '%k:%i',
        ];
        $items = DB::select('select task_id, task_name, task_detail, task_date, time_format(task_time,:time) as task_time from user_taskmanage where task_id = :task_id',$param);

        //タスク修正画面に遷移
        return view('task.task_fix',['tasks'=>$items]);
    }

    //タスク修正(post)
    public function task_fix_registration(Request $request){
        //バリデーション処理
        $rules = [
            'task_name' => 'required',
            'task_detail' => 'required',
            'task_date' => 'required|after:yesterday',
            'task_time' => 'required',
        ];
        $messages=[
                'task_name.required' => 'タスク名は必ず入力してください。',
                'task_detail.required' => 'タスクの詳細は必ず入力して下さい。',
                'task_date.required' => 'タスク日付は必ず入力して下さい。',
                'task_date.after' => 'タスク日付には今日以降の日付を入力して下さい。',
                'task_time.required' => 'タスク時間は必ず入力して下さい。',
        ];
        $validator  = Validator::make($request->all(), $rules, $messages);

        //エラー処理
        $request->session()->flash('task_errors', '入力項目に問題があります。');
        if($validator->fails()){
            return redirect('/task/fix/'.$request->task_id)
            ->withErrors($validator)
            ->withInput();
        }

        //更新処理
        $completed = '';
        $task_date = $request->task_date;
        $task_time = $request->task_time;

        //日付比較処理
        $now = Carbon::now();
        $now_date = $now->format("Y/m/d");
        $task_date = Carbon::parse($task_date);
        $task_date = $task_date->format("Y/m/d");
        //今日の日付・時間連結処理
        $task = str($task_date).str($task_time);
        $task = Carbon::parse($task);
        $task = $task->format('Y-m-d H:i:s');
        $now = $now->format('Y-m-d H:i:s');
        if($task<$now){
            $completed = "excess_incomplete";   
        }elseif($task_date==$now_date){
            $completed = "today_incomplete";
        }else{
            $completed = "future_incomplete";
        }
        $param = [
            'task_id' => $request->task_id,
            'task_name' => $request->task_name,
            'task_detail' => $request->task_detail,
            'task_date' => $task_date,
            'task_time' => $task_time,
            'completed' => $completed,
        ];

        DB::update('update user_taskmanage set task_name = :task_name, task_detail = :task_detail, task_date = :task_date, task_time = :task_time, completed = :completed where task_id = :task_id',$param);
        $request->session()->flash('update_message', 'タスクを更新しました');
        
        //タスク一覧に画面遷移する
        return redirect('task/app');
    }

    //タスク詳細(get)
    public function taskdetail(Request $request){
        $param = [
            "task_id" => $request->task_id,
            "day" => '%Y年%m月%d日',
            "time" => '%k時%i分',
        ];
        $items = DB::select('select task_name, task_detail, date_format(task_date,:day) as task_date, time_format(task_time,:time) as task_time from user_taskmanage where task_id = :task_id',$param);
        
        //タスク詳細画面に遷移
        return view('task.taskdetail',['tasks'=>$items]);
    }

    //タスク追加(get)
    public function task_insert(){
        //タスク追加画面に遷移
        return view('task.task_insert');
    }

    //タスク追加(post)
    public function task_insert_registration(Request $request){
        //バリデーション処理
        $rules = [
            'task_name' => 'required',
            'task_detail' => 'required',
            'task_date' => 'required|after:yesterday',
            'task_time' => 'required',
        ];
    
        $messages=[
                'task_name.required' => 'タスク名は必ず入力してください。',
                'task_detail.required' => 'タスクの詳細は必ず入力して下さい。',
                'task_date.required' => 'タスク日付は必ず入力して下さい。',
                'task_date.after' => 'タスク日付には今日以降の日付を入力して下さい。',
                'task_time.required' => 'タスク時間は必ず入力して下さい。',
        ];
        $validator  = Validator::make($request->all(), $rules, $messages);

        //エラー処理
        $request->session()->flash('task_errors', '入力項目に問題があります。');
        if($validator->fails()){
            return redirect('/task/add')
            ->withErrors($validator)
            ->withInput();
        }

        //タスク追加処理
        $completed  = '';
        $user_id = $request->session()->get('user_id');
        $task_date = $request->task_date;
        $task_time = $request->task_time;
        $maxId = DB::table('user_taskmanage')
            ->select('task_id')
            ->orderBy('task_id','desc')
            ->first();
        if($maxId==null){
            $task_id = 1;
        }else{
            $task_id = $maxId->task_id+1;
        }

        //日付比較処理
        $now = Carbon::now();
        $now_date = $now->format("Y/m/d");
        $task_date = Carbon::parse($task_date);
        $task_date = $task_date->format("Y/m/d");
        //今日の日付・時間連結処理
        $task = str($task_date).str($task_time);
        $task = Carbon::parse($task);
        $task = $task->format('Y-m-d H:i:s');
        $now = $now->format('Y-m-d H:i:s');
        if($task<$now){
            $completed = "excess_incomplete";   
        }elseif($task_date==$now_date){
            $completed = "today_incomplete";
        }else{
            $completed = "future_incomplete"
        }
        $param = [
            'user_id' => $user_id,
            'task_id' => $task_id,
            'task_name' => $request->task_name,
            'task_detail' => $request->task_detail,
            'task_date' => $task_date,
            'task_time' => $task_time,
            'completed' => $completed,
        ];

        DB::insert('insert into user_taskmanage values(:task_id, :task_name, :task_detail, :task_date, :task_time, :user_id, :completed)',$param);
        $request->session()->flash('insert_message', 'タスクを追加しました。');
        
        //タスク一覧画面に遷移
        return redirect('/task/app');
    }

    //タスク一覧(get)
    public function taskapp_list(Request $request){
        $user_id = $request->session()->get('user_id');

        //ユーザ権限確認
        $user_param = [
            'user_id' => $user_id,
        ];
        $get_admin = DB::select('select admin from user where user_id = :user_id',$user_param);
        $request->session()->put('admin',$get_admin[0]->admin);
        $user_admin = $get_admin[0]->admin;

        //タスク権限更新処理
        self::usertask_date_update($user_id);

        if($user_admin == "admin"){
            $param = [
                "day" => '%Y年%m月%d日',
                "time" => '%k時%i分',
            ];
            $items = DB::select('select task_id, task_name, task_detail, date_format(task_date,:day) as task_date, time_format(task_time,:time) as task_time, user_id, completed from user_taskmanage order by user_id',$param);
        }else{
            $param = [
                "user_id" => $user_id,
                "day" => '%Y年%m月%d日',
                "time" => '%k時%i分',
            ];
            $items = DB::select('select task_id, task_name, task_detail, date_format(task_date,:day) as task_date, time_format(task_time,:time) as task_time, completed from user_taskmanage where user_id=:user_id',$param);
        }
        $informations = DB::select('select information_id, information_name, information_detail, information_date from information_board order by information_date');
        
        //コロナapi呼び出し
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

        //現在時間取得
        $date = date("Y年m月d日 H時i分s秒"); 

        //タスク一覧画面に遷移
        return view('task.tasktop_top',['tasks'=>$items, 'api'=>$data, 'date'=>$date, 'informations'=>$informations]);
    }

    //タスク削除(get)
    public function task_delete(Request $request){
        $param = [
            'task_id' => $request->task_id,
        ];
        //指定したタスクID以上のタスクIDを昇順抽出
        $task_ids = DB::select('select task_id from user_taskmanage where task_id > :task_id order by task_id asc',$param);
        DB::delete('delete from user_taskmanage where task_id = :task_id',$param);
        foreach($task_ids as $task_id){
            $param = [
                'task_id' => $task_id->task_id,
            ];
            DB::update('update user_taskmanage set task_id = :task_id - 1 where task_id = :task_id ',$param);
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
    public function taskapp_login_registration(Request $request){
        //セッション削除
        $request->session()->flush();
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
            return redirect('/task')
            ->withErrors($validator)
            ->withInput();
        }

        //ユーザ認証
        $param=[
            'user_email' => $request->email,
            'user_password' => $request->password,
        ];
        $items = DB::select('select user_id, admin from user where user_email=:user_email and user_pass=:user_password',$param);
        foreach($item as $user){
            $user_id = $user->user_id;
            $user_admin = $user->admin;
        }

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
        //バリデーション処理
        $rules = [
            'user_name' => 'required',
            'user_password' => 'required',
            'user_email' => 'required|email',
        ];
        $messages=[
                'user_email.required' => 'メールアドレスは必ず入力してください。',
                'user_email.email' => 'メールアドレスは適切な書式で入力してください。',
                'user_password.required' => 'パスワードは必ず入力して下さい。',
                'user_name.required' => 'ユーザ名は必ず入力して下さい。',
        ];
        $validator  = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return redirect('/login/insert')
            ->withErrors($validator)
            ->withInput();
        }
        
        //新規会員登録処理
        $user_name = $request->user_name;
        $user_password = $request->user_password;
        $user_email = $request->user_email;
        $maxId = DB::table('user')
            ->select('user_id')
            ->orderBy('user_id','desc')
            ->first();
            
        if($maxId==null){
            $user_id = 1;
        }else{
            $user_id = $maxId->user_id+1;
        }
        $param = [
            'user_id' => $user_id,
            'user_name' => $request->user_name,
            'user_password' => $request->user_password,
            'user_email' => $request->user_email,
            'admin' => "user",
        ];
        DB::insert('insert into user(user_id, user_name, user_pass, user_email) values(:user_id, :user_name, :user_password, :user_email)',$param);
        $request->session()->flash('insert_message', 'ユーザを追加しました。');
        
        //ログイン画面に遷移
        return redirect('/task');
    }

    //csv出力
    public function task_csv(Request $request)
    {
        $user_id = $request->session()->get('user_id');
        $param = [
            "user_id" => $user_id,
            "day" => '%Y年%m月%d日',
            "time" => '%k時%i分',
        ];
        $items = DB::select('select task_id, task_name, task_detail, date_format(task_date,:day) as task_date, time_format(task_time,:time) as task_time from user_taskmanage where user_id=:user_id',$param);
        $data = [];
        $data[] = ['No', 'タスク名', 'タスク詳細', 'タスク日付', 'タスク時間'];
        foreach($items as $item){
            $data[] = [$item->task_id,$item->task_name,$item->task_detail,$item->task_date,$item->task_time];
        }
        $save_file = 'C:\task.csv';
        $file = new \SplFileObject($save_file, 'w'); // ファイルが無ければ作成
        $file->setCsvControl(",");                   // カンマ区切り
        foreach ($data as $row) {
            mb_convert_variables('SJIS', 'UTF-8', $row);
            $file->fputcsv($row);
        }

        // 出力バッファをopen
        $stream = fopen('php://output', 'w');
        // 文字コードをShift-JISに変換
        stream_filter_prepend($stream,'convert.iconv.utf-8/cp932//TRANSLIT');
        // ヘッダー
        fputcsv($stream, ['No', 'タスク名', 'タスク詳細', 'タスク日付', 'タスク時間']);
        // データ
        foreach ($items as $item) {
            fputcsv($stream, [$item->task_id,$item->task_name,$item->task_detail,$item->task_date,$item->task_time]);
        }
        fclose($stream);
        $request->session()->flash('csvoutput_message', 'csv出力が完了しました。');

        //タスク一覧画面に遷移
        return redirect('/task/app');
    }

    //タスク追加(get)
    public function taskocr_p(){
        //タスク追加画面に遷移
        return view('task.taskocr');
    }

    //python呼び出し
    public function taskocr(Request $request){
        if(strpos($request->file_name,'pdf') !== false){
            $command = "python ../app/Http/Controllers/ocr.py ".$request->file_name;
        }else{
            $command = "python ../app/Http/Controllers/pictureocr.py ".$request->file_name;
        }
        exec($command , $outputs);
        $data = mb_convert_encoding ($outputs , 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');

        //ocr画面に遷移
        return view('task.taskocr', ['ocr' => $data]);
    }

    //郵便番号検索画面遷移
    public function taskzipcode(){
        //タスク追加画面に遷移
        return view('task.taskzipcode');
    }

    //住所取得
    public function taskgetzipcode(Request $request){
        // APIアクセスURL
        $url = 'http://zipcloud.ibsnet.co.jp/api/search?zipcode='.$request->zipcode;
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

        $data1 = [$result["results"][0]["address1"].$result["results"][0]["address2"].$result["results"][0]["address3"]];
        $data2 = [$result["results"][0]["kana1"].$result["results"][0]["kana2"].$result["results"][0]["kana3"]];
        $resultdata = array_merge($data1,$data2);

        //ocr画面に遷移
        return view('task.taskzipcode', ['zipcode' => $resultdata]);
    }

    //タスク完了更新処理
    public function tasksuccess(Request $request){
        $param = [
            'task_id' => $request->task_id,
            'completed' => 'complete',
        ];
        DB::update('update user_taskmanage set completed = :completed where task_id = :task_id ',$param);
        $request->session()->flash('completed_message', 'タスクを完了済みに更新しました。');
        
        //タスク一覧画面に遷移
        return redirect('/task/app');
    }

    //タスク完了取消処理
    public function tasksuccessdenger(Request $request){
        //日付更新更新処理
        self::date_excess_update($request->task_id);
        $param = [
            'task_id' => $request->task_id,
        ];
        $datas = DB::select('select completed, task_date from user_taskmanage where task_id = :task_id and completed != "excess_incomplete"',$param);
        if(!empty($datas)){
            foreach($datas as $task_date){
                $date = $task_date->task_date;
             }
             $today = Carbon::today();
             $date = Carbon::parse($date);
             $work_date = $date->format("Y-m-d");
             $work_today = $today->format("Y-m-d");
             if($work_today<$work_date){
                 $completed = "future_incomplete";   
             }else{
                 $completed = "today_incomplete";
             }
             $param = [
                'task_id' => $request->task_id,
                'completed' => $completed,
             ];
             DB::update('update user_taskmanage set completed = :completed where task_id = :task_id ',$param);
        }
        $request->session()->flash('incomplete_message', 'タスクの完了を取り消しました');
        
        //タスク一覧画面に遷移
        return redirect('/task/app');
    }

    //タスク日付更新処理
    public function date_excess_update($task_id){
        $param = [
            'task_id' => $task_id,
        ];
        $datas = DB::select('select task_date, task_time from user_taskmanage where task_id = :task_id ',$param);
        foreach($datas as $select){
            $task_date = $select->task_date;
            $task_time = $select->task_time;
        }
        $task = str($task_date).str($task_time);
        $task_date = Carbon::parse($task);
        $task_date = $task_date->format('Y-m-d H:i:s');
        $param = [
            'task_id' => $task_id,
            'task_date' => $task_date,
        ];
        DB::update('update user_taskmanage set completed = "excess_incomplete" where :task_date < now() and task_id = :task_id',$param);
    }

    //タスク日付一斉更新処理
    public function date_excess_allupdate(){
        $datas = DB::select('select task_id, task_date, task_time from user_taskmanage');
        foreach($datas as $select){
            $task_id = $select->task_id;
            $task_date = $select->task_date;
            $task_time = $select->task_time;
            $task = str($task_date).str($task_time);
            $task_date = Carbon::parse($task);
            $task_date = $task_date->format('Y-m-d H:i:s');
            $param = [
                'task_date' => $task_date,
                'task_id' => $task_id,
            ];
            DB::update('update user_taskmanage set completed = "excess_incomplete" where :task_date < now() and task_id = :task_id',$param);
        }
    }
}

