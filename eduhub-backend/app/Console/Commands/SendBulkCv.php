<?php

namespace App\Console\Commands;

use App\Mail\SendCvMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBulkCv extends Command
{
    protected $signature = 'send:bulk-cv';
    protected $description = 'Send CV and intro email to multiple companies';

    public function handle()
    {
        $emails = [
            "m.khairy@wakeb.tech",
            "mohamed.khairy.eg@gmail.com",
            // "CV@Arabianfal.com",
            // "Cv@aldawaa.com.sa",
            // "Hr.qrm@hotmail.com",
            // "Rec@alojaimi.com",
            // "Recruitment@rawabiholding.com",
            // "Job.s6@hotmail.com",
            // "Careers@dnata.com",
            // "Hr@alesayi_motors.com",
            // "Hr@alarayan.com",
            // "Recruiting.ksa@mcmermott.com",
            // "Recruitment@aytb.com",
            // "Wadaef@gtecorp.com",
            // "Careers@nesr.com",
            // "Recruit@farm.com.sa",
            // "Info@alhumamlaw.com",
            // "Hr1@gulfteksaudi.com",
            // "Catcosa@catcosa.com",
            // "Cv@tafear.com",
            // "Recruitment@sraco.com.sa",
            // "Career@sidco.com.sa",
            // "Info@atco.com.sa",
            // "HRdepartmental@sa.yokogawa.com",
            // "Recruitment@shadeco.com",
            // "Klc.hr@alkafaa.com",
            // "Kbr-amcdehr@kbr.com",
            // "Recruitment@batook.com",
            // "Career@shawarmer.com",
            // "Hrsupport@archirodon.net",
            // "Jobs@binajinah.com",
            // "Marketing.np@nesma.com",
            // "Wadaef2019@abdulla-fouad.com",
            // "Careers@innosoft.sa",
            // "M@startime.com.sa",
            // "job@musk.sa.com",
            // "Shababwatansa@gmail.com",
            // "j@sabksa.com",
            // "al.alshaikh@bonyan.sa",
            // "ymohammed@innovest.com.sa",
            // "al.alshaikh@bonyan.sa", // duplicate
            // "s.alwadi@nhc.sa",
            // "i.atassi@artar.com.sa",
            // "oalkhunaizi@darwaemaar.com",
            // "jobs@almasah.net",
            // "recruitment.amjad@gmail.com",
            // "jobrydlaw@gmail.com",
            // "Job@almoosahospital.com.sa",
            // "recruitment@almoosahospital.com.sa",
            // "jobs@sghgroup.net",
            // "career.dmm@sghgroup.net",
            // "talent.acquisition@drsulaimanalhabib.com",
            // "Careers@jhah.com",
            // "hrd@alahsahospital.com.sa",
            // "career@almanahospital.com.sa",
            // "info@familycare.com.sa",
            // "info@ramclinics.com",
            // "HR.DSFHR@fakeeh.care",
            // "career@mouwasat.com",
            // "careers@dallah-hospital.com",
            // "hr.phc@drsulaimanalhabib.com",
            // "careers@almurjanhospital.com",
            // "hiringnow.ksa@gmail.com",
            // "recruitment@wecareksa.com"
        ];

        $cvPath = public_path('Mohamed Khairy Software Engineer 2025.pdf');

        $name = 'Mohamed Khairy';

        foreach ($emails as $email) {
            Mail::to($email)->send(new SendCvMail($name, $cvPath));
        }

        $this->info('All emails have been sent successfully.');
    }
}
