<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Mail;
use App\Mail\SendMail;

class MailSendController extends Controller
{
	public function postPurchaseComplete(Request $request){
		$user_id = $request->session()->get('user_id');
		$items = [];
		$today_task=array();
		$old_task=array();
		$today_count = 1;
		$old_count = 1;
		//今日の日付取得
		$today = date("Y年m月d日");
		//ユーザ情報取得
		$userparam = [
            "user_id" => $user_id,
        ];
		$userdata = DB::select('select user_name, user_email, admin from user where user_id=:user_id',$userparam);
		$admin= $userdata[0]->admin;
		if($admin == "admin"){
			$user_name = 'タスク管理アプリ管理者';
		}else{
			$user_name = $userdata[0]->user_name;
		}
		$mail_to = $userdata[0]->user_email;
		//admin処理・user処理
		if($admin == 'admin'){
			//今日のタスク取得
			$param = [
				"start_day" => 'YYYY年MM月DD日',
				"start_time" => 'HH24時MI分',
				"end_day" => 'YYYY年MM月DD日',
				"end_time" => 'HH24時MI分',
				'completed' => 'today_incomplete',
				'completedf' => 'excess_incomplete',
			];
			$items = DB::select('select t.task_name as task_name, t.task_detail as task_detail, to_char(t.task_start_date,:start_day) as task_start_date, to_char(t.task_start_time,:start_time) as task_start_time, to_char(t.task_end_date,:end_day) as task_end_date, to_char(t.task_end_time,:end_time) as task_end_time, t.completed as task_completed, u.user_id as user_id, u.user_name as user_name, u.user_email as user_email from user_taskmanage t inner join user u on t.user_id = u.user_id where t.completed = :completed or t.completed = :completedf order by task_start_date, task_start_time, task_end_date, task_end_time',$param);
			foreach($items as $data){
				//今日のタスク
				if($data->task_completed == "today_incomplete"){
					$work = array(array(str($today_count),$data->task_name,$data->task_detail,$data->task_start_date,$data->task_start_time,$data->task_end_date,$data->task_end_time,$data->user_id,$data->user_name,$data->user_email));
					$today_task = array_merge($today_task,$work);
					$today_count += 1;
				}
				//未完了タスク(今日以前)
				if($data->task_completed == "excess_incomplete"){
					$work = array(array(str($old_count),$data->task_name,$data->task_detail,$data->task_start_date,$data->task_start_time,$data->task_end_date,$data->task_end_time,$data->user_id,$data->user_name,$data->user_email));
					$old_task = array_merge($old_task,$work);
					$old_count += 1;
				}
			}

		}else{
			$param = [
				"user_id" => $user_id,
				"start_day" => 'YYYY年MM月DD日',
				"start_time" => 'HH24時MI分',
				"end_day" => 'YYYY年MM月DD日',
				"end_time" => 'HH24時MI分',
				'completed' => 'today_incomplete',
				'completedf' => 'excess_incomplete',
			];
			$items = DB::select('select task_name, task_detail, to_char(task_start_date,:start_day) as task_start_date, to_char(task_start_time,:start_time) as task_start_time, to_char(task_end_date,:end_day) as task_end_date, to_char(task_end_time,:end_time) as task_end_time, completed from user_taskmanage where user_id=:user_id and (completed = :completed or completed = :completedf) order by task_start_date, task_start_time, task_end_date, task_end_time',$param);
			foreach($items as $data){
				//今日のタスク
				if($data->completed == "today_incomplete"){
					$work = array(array(str($today_count),$data->task_name,$data->task_detail,$data->task_start_date,$data->task_start_time,$data->task_end_date,$data->task_end_time));
					$today_task = array_merge($today_task,$work);
					$today_count += 1;
				}
				//未完了タスク(今日以前)
				if($data->completed == "excess_incomplete"){
					$work = array(array(str($old_count),$data->task_name,$data->task_detail,$data->task_start_date,$data->task_start_time,$data->task_end_date,$data->task_end_time));
					$old_task = array_merge($old_task,$work);
					$old_count += 1;
				}
			}
		}
		//csv関数呼び出し
		$csv_flg = self::csvoutput($admin, $today_task, $old_task);
		//メール送信
		Mail::to($mail_to)->send( new SendMail($user_name, $mail_to, $today_task, $old_task, $admin, $csv_flg));
		$request->session()->flash('sendmail_message', 'メール送信が完了しました');
		
		//タスク一覧画面に遷移
        return redirect('/task/app');
	}

	public static function batchEmailSending(){
		//user_id,admin取得
		$user_data = DB::select('select user_id, user_email, user_name, admin from user');
		foreach($user_data as $data){
			$items = [];
			$csv_flg;
			$today_task=array();
			$old_task=array();
			$today_count = 1;
			$old_count = 1;
			$select_user = $data->user_id;
			$select_email = $data->user_email;
			$select_admin = $data->admin;
			$select_name = $data->user_name;
			if($select_admin == "admin"){
				$user_name = 'タスク管理アプリ管理者';
			}else{
				$user_name = $select_name;
			}
			$mail_to = $select_email;
			//admin処理・user処理
			if($select_admin == 'admin'){
				$param = [
					"start_day" => '%Y年%m月%d日',
                    "start_time" => '%k時%i分',
                    "end_day" => '%Y年%m月%d日',
                    "end_time" => '%k時%i分',
					'completed' => 'today_incomplete',
					'completedf' => 'excess_incomplete',
				];
				$items = DB::select('select t.task_name as task_name, t.task_detail as task_detail, date_format(t.task_start_date,:start_day) as task_start_date, time_format(t.task_start_time,:start_time) as task_start_time, date_format(t.task_end_date,:end_day) as task_end_date, time_format(t.task_end_time,:end_time) as task_end_time, t.completed as task_completed, u.user_id as user_id, u.user_name as user_name, u.user_email as user_email from user_taskmanage t inner join user u on t.user_id = u.user_id where t.completed = :completed or t.completed = :completedf order by task_start_date, task_start_time, task_end_date, task_end_time',$param);
				if(!empty($items)){
					foreach($items as $datas){
						//今日のタスク
						if($datas->task_completed == "today_incomplete"){
							$work = array(array(str($today_count),$datas->task_name,$datas->task_detail,$datas->task_start_date,$datas->task_start_time,$datas->task_end_date,$datas->task_end_time,$datas->user_id,$datas->user_name,$datas->user_email));
							$today_task = array_merge($today_task,$work);
							$today_count += 1;
						}
						//未完了タスク(今日以前)
						if($datas->task_completed == "excess_incomplete"){
							$work = array(array(str($old_count),$datas->task_name,$datas->task_detail,$datas->task_start_date,$datas->task_start_time,$datas->task_end_date,$datas->task_end_time,$datas->user_id,$datas->user_name,$datas->user_email));
							$old_task = array_merge($old_task,$work);
							$old_count += 1;
						}
					}
				}
			}else{
				$param = [
					"user_id" => $select_user,
					"start_day" => '%Y年%m月%d日',
                    "start_time" => '%k時%i分',
                    "end_day" => '%Y年%m月%d日',
                    "end_time" => '%k時%i分',
					'completed' => 'today_incomplete',
					'completedf' => 'excess_incomplete',
				];
				$items = DB::select('select task_name, task_detail, date_format(task_start_date,:start_day) as task_start_date, time_format(task_start_time,:start_time) as task_start_time, date_format(task_end_date,:end_day) as task_end_date, time_format(task_end_time,:end_time) as task_end_time, completed from user_taskmanage where user_id=:user_id and (completed = :completed or completed = :completedf) order by task_start_date, task_start_time, task_end_date, task_end_time',$param);
				if(!empty($items)){
					foreach($items as $datas){
						//今日のタスク
						if($datas->completed == "today_incomplete"){
							$work = array(array(str($today_count),$datas->task_name,$datas->task_detail,$datas->task_start_date,$datas->task_start_time,$datas->task_end_date,$datas->task_end_time));
							$today_task = array_merge($today_task,$work);
							$today_count += 1;
						}
						//未完了タスク(今日以前)
						if($datas->completed == "excess_incomplete"){
							$work = array(array(str($old_count),$datas->task_name,$datas->task_detail,$datas->task_start_date,$datas->task_start_time,$datas->task_end_date,$datas->task_end_time));
							$old_task = array_merge($old_task,$work);
							$old_count += 1;
						}
					}
				}
			}
			//csv関数呼び出し
			$csv_flg = self::csvoutput($select_admin, $today_task, $old_task);
			//メール送信
			Mail::to($mail_to)->send( new SendMail($user_name, $mail_to, $today_task, $old_task, $select_admin, $csv_flg));
		}
	}

	//csv関数
	public static function csvoutput($text, $today_task, $old_task){
		//変数定義
		$csv_count = 1;
		$data = [];
		$csvw_flg = false;
		if($text == "admin"){
			//csvファイル作成
			if(!empty($today_task)){
				$data[] = ['本日のタスク'];
				$data[] = ['No', 'ユーザID', 'ユーザ名', 'emailアドレス', 'タスク名', 'タスク詳細', 'タスク開始日付', 'タスク開始時間', 'タスク終了日付', 'タスク終了時間'];
				foreach($today_task as $item){
					$data[] = [$item[0],$item[7],$item[8],$item[9],$item[1],$item[2],$item[3],$item[4],$item[5],$item[6]];
				}
				$csvw_flg = true;
			}
			if(!empty($old_task)){
				$data[] = ['未完了のタスク'];
				$data[] = ['No', 'ユーザID', 'ユーザ名', 'emailアドレス', 'タスク名', 'タスク詳細', 'タスク開始日付', 'タスク開始時間', 'タスク終了日付', 'タスク終了時間'];
				foreach($old_task as $item){
					$data[] = [$item[0],$item[7],$item[8],$item[9],$item[1],$item[2],$item[3],$item[4],$item[5],$item[6]];
				}
				$csvw_flg = true;
			}
			if($csvw_flg == true){
				$save_file = storage_path('task.csv');
				$file = new \SplFileObject($save_file, 'w'); // ファイルが無ければ作成
				$file->setCsvControl(",");                   // カンマ区切り
				foreach ($data as $row) {
					mb_convert_variables('SJIS', 'UTF-8', $row);
					$file->fputcsv($row);
				}
			}
		}else{
			if(!empty($today_task)){
				//csvファイル作成
				$data[] = ['本日のタスク'];
				$data[] = ['No', 'タスク名', 'タスク詳細', 'タスク開始日付', 'タスク開始時間', 'タスク終了日付', 'タスク終了時間'];
				foreach($today_task as $item){
					$data[] = [$item[0],$item[1],$item[2],$item[3],$item[4],$item[5],$item[6]];
				}
				$csvw_flg = true;
			}
			if(!empty($old_task)){
				$data[] = ['未完了のタスク'];
				$data[] = ['No', 'タスク名', 'タスク詳細', 'タスク開始日付', 'タスク開始時間', 'タスク終了日付', 'タスク終了時間'];
				foreach($old_task as $item){
					$data[] = [$item[0],$item[1],$item[2],$item[3],$item[4],$item[5],$item[6]];
				}
				$csvw_flg = true;
			}
			if($csvw_flg == true){
				$save_file = storage_path('task.csv');
				$file = new \SplFileObject($save_file, 'w'); // ファイルが無ければ作成
				$file->setCsvControl(",");                   // カンマ区切り
				foreach ($data as $row) {
					mb_convert_variables('SJIS', 'UTF-8', $row);
					$file->fputcsv($row);
				}
			}
		}
		return $csvw_flg;
	} 
}
