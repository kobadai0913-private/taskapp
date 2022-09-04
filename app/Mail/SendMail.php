<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $title;
    protected $text;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user_name, $email, $today_task, $old_task, $admin, $csv_flg)
    {
        $this->title = sprintf('%s様', $user_name);
        $this->email = $email;
        $this->name = $user_name;
        $this->today_task = $today_task;
        $this->old_task = $old_task;
        $this->admin = $admin;
        $this->csv_flg = $csv_flg;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->csv_flg == true){
            if($this->admin == "admin"){
                return $this->view('mail.admin_mail_send')
                        ->to($this->email, $this->name)
                        ->subject($this->title)
                        ->attach(storage_path('task.csv'))
                        ->with([
                            'today_task' => $this->today_task,
                            'old_task' => $this->old_task,
                          ]);
            }else{
                return $this->view('mail.mail_send')
                        ->to($this->email, $this->name)
                        ->subject($this->title)
                        ->attach(storage_path('task.csv'))
                        ->with([
                            'today_task' => $this->today_task,
                            'old_task' => $this->old_task,
                          ]);    
            }
        }else{
            if($this->admin == "admin"){
                return $this->view('mail.admin_mail_send')
                        ->to($this->email, $this->name)
                        ->subject($this->title)
                        ->with([
                            'today_task' => $this->today_task,
                            'old_task' => $this->old_task,
                          ]);
            }else{
                return $this->view('mail.mail_send')
                        ->to($this->email, $this->name)
                        ->subject($this->title)
                        ->with([
                            'today_task' => $this->today_task,
                            'old_task' => $this->old_task,
                          ]);    
            }
        }
    }
}