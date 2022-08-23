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
		$user_id = $request->user_id;
        $param = [
            "user_id" => $user_id,
            "day" => 'YYYY年MM月DD日',
            "time" => 'HH24時MI分',
			'completed' => 'incomplete',
        ];
        $items = DB::select('select task_id, task_name, task_detail, to_char(task_date,:day) as task_date, to_char(task_time,:time) as task_time, completed from user_taskmanage where user_id=:user_id and completed = :completed',$param);
		$userparam = [
            "user_id" => $user_id,
        ];
		$userdata = DB::select('select user_name, user_email from users where user_id=:user_id',$userparam);
		$user_name = $userdata[0]->user_name;
		$mail_text = $user_name.' 様、タスク管理アプリご利用ありがとうございます。';
		$mail_to = $userdata[0]->user_email;
		Mail::to($mail_to)->send( new SendMail($user_name, $mail_text, $mail_to, $items));
		$request->session()->flash('sendmail_message', 'メール送信が完了しました');
		//タスク一覧画面に遷移
        return redirect('/task/app');
	}

	public static function batchEmailSending(){
		//user_id取得
		$user_id = DB::select('select user_id from users');
		foreach($user_id as $data){
			$select_user = $data->user_id;
			$param = [
				"user_id" => $select_user,
				"day" => 'YYYY年MM月DD日',
				"time" => 'HH24時MI分',
				'completed' => 'incomplete',
			];

			$items = DB::select('select task_id, task_name, task_detail, to_char(task_date,:day) as task_date, to_char(task_time,:time) as task_time, completed from user_taskmanage where user_id=:user_id and completed = :completed',$param);	
			if(!empty($items)){
				$userparam = [
					"user_id" => $select_user,
				];
				$userdata = DB::select('select user_name, user_email from users where user_id=:user_id',$userparam);
				$user_name = $userdata[0]->user_name;
				$mail_text = $user_name.' 様、タスク管理アプリご利用ありがとうございます。';
				$mail_to = $userdata[0]->user_email;
				Mail::to($mail_to)->send( new SendMail($user_name, $mail_text, $mail_to, $items));
			}			
		}
	}
}
