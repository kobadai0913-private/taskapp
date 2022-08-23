<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\MailSendController;

class sendMail extends Command
{
   /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendMail';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'user_sendmail';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        MailSendController::batchEmailSending();
    }
}
