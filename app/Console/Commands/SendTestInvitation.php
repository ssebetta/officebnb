<?php

namespace App\Console\Commands;

use App\Mail\OfficeInvitationMail;
use App\Models\Office;
use App\Models\OfficeInvitation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendTestInvitation extends Command
{
    protected $signature = 'invite:send {email}';
    protected $description = 'Send a test office invitation';

    public function handle()
    {
        $email = $this->argument('email');
        
        $superAdmin = User::where('email', 'haroldpeter6@gmail.com')->first();
        if (!$superAdmin) {
            $this->error('Super admin not found');
            return 1;
        }

        $office = Office::where('owner_id', $superAdmin->id)->first();
        if (!$office) {
            $this->error('No office found for super admin');
            return 1;
        }

        $invitation = OfficeInvitation::create([
            'office_id' => $office->id,
            'email' => $email,
            'token' => Str::random(40),
            'invited_by' => $superAdmin->id,
        ]);

        Mail::to($email)->send(new OfficeInvitationMail($invitation));

        $this->info("Invitation sent to {$email} for office: {$office->title}");
        return 0;
    }
}
