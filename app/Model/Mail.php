<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Providers;

class Mail extends Model
{
    use HasFactory;

    protected $table = 'user';

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    public static $information_param = [
        "user_id" => '',
        "start_day" => 'YYYY年MM月DD日',
        "start_time" => 'HH24時MI分',
        "end_day" => 'YYYY年MM月DD日',
        "end_time" => 'HH24時MI分',
        'completed' => 'today_incomplete',
        'completedf' => 'excess_incomplete',
    ];

    public static $messages=[
            'user_email.required' => 'メールアドレスは必ず入力してください。',
            'user_email.email' => 'メールアドレスは適切な書式で入力してください。',
            'user_pass.required' => 'パスワードは必ず入力して下さい。',
            'user_name.required' => 'ユーザ名は必ず入力して下さい。',
    ];

    public static $user_param = [
        'user_id' => '',
        'user_name' => '',
        'user_password' => '',
        'user_email' => '',
        'admin' => '',
    ];
}
