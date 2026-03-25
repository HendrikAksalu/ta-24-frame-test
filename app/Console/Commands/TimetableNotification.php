<?php

namespace App\Console\Commands;

use App\Mail\Timetable;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class TimetableNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:timetable-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startDate = now()->addWeek(1)->startOfWeek();
        $endDate = now()->addWeek(1)->endOfWeek();

        $response = Http::get('https://tahveltp.edu.ee/hois_back/timetableevents/timetableSearch', [
            'from' => $startDate->toIsoString(),
            'lang' => 'ET',
            'page' => 0,
            'schoolId' => 38,
            'size' => 50,
            'studentGroups' => '4b26d1e5-11ac-4c63-840e-46c450c529ee',
            'thru' => $endDate->toIsoString(),
        ]);

        $timetableEvents = collect($response->json()['content'])
            ->sortBy(['date', 'timeStart'])
            ->groupBy(fn ($event) => Carbon::parse($event['date'])->locale('et')->dayName);

        Mail::to('test@example.com')->send(new Timetable($timetableEvents, $startDate, $endDate));
    }
}
