<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Validator;
use Timestamp;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    //タスク修正(get)
    public function taskfix(Request $request){
        $param = [
            "task_id" => $request->task_id,
            "day" => '%Y年%m月%d日',
            "time" => '%k時%i分',
        ];
        $items = DB::select('select task_id, task_name, task_detail, date_format(task_date,:day) as task_date, time_format(task_time,:time) as task_time from user_taskmanage where task_id = :task_id',$param);
        
        //タスク修正画面に遷移
        return view('task.taskfix',['tasks'=>$items]);
    }

    //タスク修正(post)
    public function taskfix_create(Request $request){
        //バリデーション処理
        $rules = [
            'task_name' => 'required',
            'task_detail' => 'required',
            'task_date' => 'required|after:today|date_format:Y年m月d日',
            'task_time' => 'required|date_format:H時i分',
        ];
        $messages=[
                'task_name.required' => 'タスク名は必ず入力してください。',
                'task_detail.required' => 'タスクの詳細は必ず入力して下さい。',
                'task_date.required' => 'タスク日付は必ず入力して下さい。',
                'task_date.after' => 'タスク日付には今日以降の日付を入力して下さい。',
                'task_date.date_format' => 'タスク日付は指定のフォーマットで入力してください。',
                'task_time.required' => 'タスク時間は必ず入力して下さい。',
                'task_time.date_format' => 'タスク時間は指定のフォーマットで入力してください。',
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
        $datetime = $request->task_date." ".$request->task_time;
        $datetime = str_replace('年','/',$datetime);
        $datetime = str_replace('月','/',$datetime);
        $datetime = str_replace('日','/',$datetime);
        $datetime = str_replace('時',':',$datetime);
        $datetime = str_replace('分','',$datetime);
        $carbon = Carbon::parse($datetime);
        $date = $carbon->format("Y-m-d H:i:s");
        $param = [
            "task_id" => $request->task_id,
            "task_name" => $request->task_name,
            "task_detail" => $request->task_detail,
            'task_date' => $date,
            'task_time' => $date,
        ];
        DB::update('update user_taskmanage set task_name = :task_name, task_detail = :task_detail, task_date = :task_date, task_time = :task_time where task_id = :task_id',$param);
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
    public function taskinsert_p(){
        //タスク追加画面に遷移
        return view('task.taskinsert');
    }

    //タスク追加(post)
    public function taskinsert(Request $request){
        //バリデーション処理
        $rules = [
            'task_name' => 'required',
            'task_detail' => 'required',
            'task_date' => 'required|after:today|date_format:Y年m月d日',
            'task_time' => 'required|date_format:H時i分',
        ];
    
        $messages=[
                'task_name.required' => 'タスク名は必ず入力してください。',
                'task_detail.required' => 'タスクの詳細は必ず入力して下さい。',
                'task_date.required' => 'タスク日付は必ず入力して下さい。',
                'task_date.after' => 'タスク日付には今日以降の日付を入力して下さい。',
                'task_date.date_format' => 'タスク日付は指定のフォーマットで入力してください。',
                'task_time.required' => 'タスク時間は必ず入力して下さい。',
                'task_time.date_format' => 'タスク時間は指定のフォーマットで入力してください。',
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
        $user_id = $request->session()->get('user_id');
        $datetime = $request->task_date." ".$request->task_time;
        $datetime = str_replace('年','/',$datetime);
        $datetime = str_replace('月','/',$datetime);
        $datetime = str_replace('日','/',$datetime);
        $datetime = str_replace('時',':',$datetime);
        $datetime = str_replace('分','',$datetime);
        $carbon = Carbon::parse($datetime);
        $date = $carbon->format("Y-m-d H:i:s");
        $maxId = DB::table('user_taskmanage')
            ->select('task_id')
            ->orderBy('task_id','desc')
            ->first();
        if($maxId==null){
            $task_id = 1;
        }else{
            $task_id = $maxId->task_id+1;
        }
        $param = [
            'user_id' => $user_id,
            'task_id' => $task_id,
            'task_name' => $request->task_name,
            'task_detail' => $request->task_detail,
            'task_date' => $date,
            'task_time' => $date,
        ];
        DB::insert('insert into user_taskmanage values(:task_id, :task_name, :task_detail, :task_date, :task_time, :user_id)',$param);
        $request->session()->flash('insert_message', 'タスクを追加しました。');
        
        //タスク一覧画面に遷移
        return redirect('/task/app');
    }

    //タスク一覧(get)
    public function taskapp(Request $request){
            $user_id = $request->session()->get('user_id');
            $param = [
                "user_id" => $user_id,
                "day" => '%Y年%m月%d日',
                "time" => '%k時%i分',
            ];
            $items = DB::select('select task_id, task_name, task_detail, date_format(task_date,:day) as task_date, time_format(task_time,:time) as task_time from user_taskmanage where user_id=:user_id',$param);
            
            //コロナapi呼び出し
            $command = "python ../app/Http/Controllers/api.py";
            exec($command , $outputs);
            $data = mb_convert_encoding($outputs , 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');

            //現在時間取得
            $date = date("Y年m月d日 H時i分s秒"); 

            //タスク一覧画面に遷移
            return view('task.tasktop',['tasks'=>$items, 'user_id'=>$user_id, 'api'=>$data, 'date'=>$date]);
    }

    //タスク削除(get)
    public function taskdelete(Request $request){
        $param = [
            'task_id' => $request->task_id,
        ];
        DB::delete('delete from user_taskmanage where task_id = :task_id',$param);
        DB::update('update user_taskmanage set task_id = task_id - 1 where task_id > :task_id ',$param);
        $request->session()->flash('delete_message', 'タスクを削除しました。');
        
        //タスク一覧画面に遷移
        return redirect('/task/app');
    }

    //ログイン(get)
    public function tasklogin(){
        //ログイン画面に遷移
        return view('task.tasklogin');
    }

    //ログイン(post)
    public function taskloginsucsess(Request $request){
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
        $item = DB::select('select user_id from user where user_email=:user_email and user_pass=:user_password',$param);

        //エラー処理
        if($item==null){
            $request->session()->flash('login_errors', '入力項目に問題があります。');
            return redirect('/task');
        }else{
            foreach($item as $it){
                $user_id = $it->user_id;
            }
            $param = [
                "user_id" => $user_id,
                "day" => '%Y年%m月%d日',
                "time" => '%k時%i分',
            ];
            $items = DB::select('select task_id, task_name, task_detail, date_format(task_date,:day) as task_date, time_format(task_time,:time) as task_time from user_taskmanage where user_id=:user_id',$param);
            $request->session()->put('user_id', $user_id);
        }   
        
        //コロナapi呼び出し
        $command = "python ../app/Http/Controllers/api.py";
        exec($command , $outputs);
        $data = mb_convert_encoding($outputs , 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');

        //現在時間取得
        $date = date("Y年m月d日 H時i分s秒"); 

        //タスク一覧画面に遷移
        return view('task.tasktop',['tasks'=>$items, 'user_id'=>$user_id, 'api'=>$data, 'date'=>$date]);

    }

    //新規会員登録(get)
    public function logininsert(){
        return view('task.logininsert');
    }

    //新規会員登録(post)
    public function loginsucsess(Request $request){
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
        ];
        DB::insert('insert into user(user_id, user_name, user_pass, user_email) values(:user_id, :user_name, :user_password, :user_email)',$param);
        $request->session()->flash('insert_message', 'ユーザを追加しました。');
        
        //ログイン画面に遷移
        return redirect('/task');
    }

    //csv出力
    public function taskcsv(Request $request)
    {
        $user_id = $request->user_id;
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
        $save_file = '../csv/task.csv';
        $file = new \SplFileObject($save_file, 'w'); // ファイルが無ければ作成
        $file->setCsvControl(",");                   // カンマ区切り
        foreach ($data as $row) {
            mb_convert_variables('SJIS', 'UTF-8', $row);
            $file->fputcsv($row);
        }
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
        $command = "python ../app/Http/Controllers/zipcodeapi.py ".$request->zipcode;
        exec($command , $outputs);
        $data = mb_convert_encoding ($outputs , 'UTF-8', 'ASCII, JIS, UTF-8, SJIS');

        //ocr画面に遷移
        return view('task.taskzipcode', ['zipcode' => $data]);
    }
}
