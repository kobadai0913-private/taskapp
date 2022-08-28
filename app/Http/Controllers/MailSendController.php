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
		$count = 1;
		//今日の日付取得
		$today = date("Y年m月d日");
		//ユーザ情報取得
		$userparam = [
            "user_id" => $user_id,
        ];
		$userdata = DB::select('select user_name, user_email, admin from users where user_id=:user_id',$userparam);
		$admin= $userdata[0]->admin;
		if($admin == "admin"){
			$user_name = 'タスク管理アプリ管理者';
		}else{
			$user_name = $userdata[0]->user_name;
		}
		$mail_to = $userdata[0]->user_email;
		//admin処理・user処理
		if($admin == 'admin'){
			$param = [
				"day" => 'YYYY年MM月DD日',
				"time" => 'HH24時MI分',
				'completed' => 'incomplete',
			];
			$items = DB::select('select t.task_name as task_name, t.task_detail as task_detail, to_char(t.task_date,:day) as task_date, to_char(t.task_time,:time) as task_time, u.user_id as user_id, u.user_name as user_name, u.user_email as user_email from user_taskmanage t inner join users u on t.user_id = u.user_id where t.completed = :completed',$param);
			foreach($items as $data){
				//タスク日付取得
				$time_work = date($data->task_date);
				//今日のタスク
				if($today == $time_work){
					$work = array(array(str($count),$data->task_name,$data->task_detail,$data->task_date,$data->task_time,$data->user_id,$data->user_name,$data->user_email));
					$today_task = array_merge($today_task,$work);
				}
				//未完了タスク(今日以前)
				if($today > $time_work){
					$work = array(array(str($count),$data->task_name,$data->task_detail,$data->task_date,$data->task_time,$data->user_id,$data->user_name,$data->user_email));
					$old_task = array_merge($old_task,$work);
				}
				$count += 1;
			}

			//csvファイル作成
			$csv_count = 1;
			$data = [];
			$data[] = ['No', 'ユーザID', 'ユーザ名', 'ユーザメールアドレス', 'タスク名', 'タスク詳細', 'タスク日付', 'タスク時間'];
			foreach($items as $item){
				$data[] = [str($csv_count),$item->user_id,$item->user_name,$item->user_email,$item->task_name,$item->task_detail,$item->task_date,$item->task_time];
				$csv_count += 1;
			}
			$save_file = storage_path('task.csv');
			$file = new \SplFileObject($save_file, 'w'); // ファイルが無ければ作成
			$file->setCsvControl(",");                   // カンマ区切り
			foreach ($data as $row) {
				mb_convert_variables('SJIS', 'UTF-8', $row);
				$file->fputcsv($row);
			}
		}else{
			$param = [
				"user_id" => $user_id,
				"day" => 'YYYY年MM月DD日',
				"time" => 'HH24時MI分',
				'completed' => 'incomplete',
			];
			$items = DB::select('select task_name, task_detail, to_char(task_date,:day) as task_date, to_char(task_time,:time) as task_time from user_taskmanage where user_id=:user_id and completed = :completed',$param);
			foreach($items as $data){
				//タスク日付取得
				$time_work = date($data->task_date);
				//今日のタスク
				if($today == $time_work){
					$work = array(array(str($count),$data->task_name,$data->task_detail,$data->task_date,$data->task_time));
					$today_task = array_merge($today_task,$work);
				}
				//未完了タスク(今日以前)
				if($today > $time_work){
					$work = array(array(str($count),$data->task_name,$data->task_detail,$data->task_date,$data->task_time));
					$old_task = array_merge($old_task,$work);
				}
				$count += 1;
			}
		}
		//csv関数呼び出し
		self::csvoutput($admin, $items);
		//メール送信
		Mail::to($mail_to)->send( new SendMail($user_name, $mail_to, $today_task, $old_task, $admin));
		$request->session()->flash('sendmail_message', 'メール送信が完了しました');
		
		//タスク一覧画面に遷移
        return redirect('/task/app');
	}

	public static function batchEmailSending(){
		//user_id,admin取得
		$user_data = DB::select('select user_id, user_email, user_name, admin from users');
		foreach($user_data as $data){
			$items = [];
			$today_task=array();
			$old_task=array();
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
			$param = [
				"user_id" => $select_user,
				"day" => 'YYYY年MM月DD日',
				"time" => 'HH24時MI分',
				'completed' => 'incomplete',
			];
			//admin処理・user処理
			if($select_admin == 'admin'){
				$param = [
					"day" => 'YYYY年MM月DD日',
					"time" => 'HH24時MI分',
					'completed' => 'incomplete',
				];
				$items = DB::select('select t.task_name as task_name, t.task_detail as task_detail, to_char(t.task_date,:day) as task_date, to_char(t.task_time,:time) as task_time, u.user_id as user_id, u.user_name as user_name, u.user_email as user_email from user_taskmanage t inner join users u on t.user_id = u.user_id where t.completed = :completed',$param);
				if(!empty($items)){
					$today_count = 1;
					$old_count = 1;
					//今日の日付取得
					$today = date("Y年m月d日");
					foreach($items as $datas){
						//タスク日付取得
						$time_work = date($datas->task_date);
						//今日のタスク
						if($today == $time_work){
							$work = array(array(str($today_count),$datas->task_name,$datas->task_detail,$datas->task_date,$datas->task_time,$datas->user_id,$datas->user_name,$datas->user_email));
							$today_task = array_merge($today_task,$work);
							$today_count += 1;
						}
						//未完了タスク(今日以前)
						if($today > $time_work){
							$work = array(array(str($old_count),$datas->task_name,$datas->task_detail,$datas->task_date,$datas->task_time,$datas->user_id,$datas->user_name,$datas->user_email));
							$old_task = array_merge($old_task,$work);
							$old_count += 1;
						}
					}
				}
			}else{
				$param = [
					"user_id" => $select_user,
					"day" => 'YYYY年MM月DD日',
					"time" => 'HH24時MI分',
					'completed' => 'incomplete',
				];
				$items = DB::select('select task_name, task_detail, to_char(task_date,:day) as task_date, to_char(task_time,:time) as task_time from user_taskmanage where user_id=:user_id and completed = :completed',$param);
				if(!empty($items)){
					$today_count = 1;
					$old_count = 1;
					//今日の日付取得
					$today = date("Y年m月d日");
					foreach($items as $datas){
						//タスク日付取得
						$time_work = date($datas->task_date);
						//今日のタスク
						if($today == $time_work){
							$work = array(array(str($today_count),$datas->task_name,$datas->task_detail,$datas->task_date,$datas->task_time));
							$today_task = array_merge($today_task,$work);
							$today_count += 1;
						}
						//未完了タスク(今日以前)
						if($today > $time_work){
							$work = array(array(str($old_count),$datas->task_name,$datas->task_detail,$datas->task_date,$datas->task_time));
							$old_task = array_merge($old_task,$work);
							$old_count += 1;
						}
					}
				}
			}
			//csv関数呼び出し
			self::csvoutput($select_admin, $items);
			//メール送信
			Mail::to($mail_to)->send( new SendMail($user_name, $mail_to, $today_task, $old_task, $select_admin));
		}
	}

	//csv関数
	public static function csvoutput($text, $items){
		//変数定義
		$csv_count = 1;
		$data = [];
		if($text == "admin"){
			//csvファイル作成
			$data[] = ['No', 'タスク名', 'タスク詳細', 'タスク日付', 'タスク時間'];
			foreach($items as $item){
				$data[] = [str($csv_count),$item->task_name,$item->task_detail,$item->task_date,$item->task_time];
				$csv_count += 1;
			}
			$save_file = storage_path('task.csv');
			$file = new \SplFileObject($save_file, 'w'); // ファイルが無ければ作成
			$file->setCsvControl(",");                   // カンマ区切り
			foreach ($data as $row) {
				mb_convert_variables('SJIS', 'UTF-8', $row);
				$file->fputcsv($row);
			}
		}else{
			//csvファイル作成
			$data[] = ['No', 'タスク名', 'タスク詳細', 'タスク日付', 'タスク時間'];
			foreach($items as $item){
				$data[] = [str($csv_count),$item->task_name,$item->task_detail,$item->task_date,$item->task_time];
				$csv_count += 1;
			}
			$save_file = storage_path('task.csv');
			$file = new \SplFileObject($save_file, 'w'); // ファイルが無ければ作成
			$file->setCsvControl(",");                   // カンマ区切り
			foreach ($data as $row) {
				mb_convert_variables('SJIS', 'UTF-8', $row);
				$file->fputcsv($row);
			}
		}
		
	} 
}
