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
    public function __construct($user_name, $text, $email, $data)
    {
        $this->title = sprintf('%s様', $user_name);
        $this->text = $text;
        $this->email = $email;
        $this->name = $user_name;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.mailsend')
                    ->to($this->email, $this->name)
                    ->subject($this->title)
                    ->with([
                        'text' => $this->text,
                        'tasks' => $this->data,
                      ]);
    }
}