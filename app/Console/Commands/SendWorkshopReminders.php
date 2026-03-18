<?php

namespace App\Console\Commands;

use App\Contracts\Repositories\WorkshopRepositoryInterface;
use App\Mail\WorkshopReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWorkshopReminders extends Command
{
    protected $signature = 'academy:remind';

    protected $description = 'Send reminder emails to all participants of tomorrow\'s workshops';

    public function __construct(
        private readonly WorkshopRepositoryInterface $workshopRepository,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $tomorrow = now()->addDay();
        $workshops = $this->workshopRepository->getWorkshopsForDate($tomorrow);

        $count = 0;

        foreach ($workshops as $workshop) {
            foreach ($workshop->enrollments as $enrollment) {
                Mail::to($enrollment->user)->queue(
                    new WorkshopReminderMail($enrollment->user, $workshop)
                );
                $count++;
            }
        }

        $this->info("Inviate {$count} email di reminder per ".$workshops->count().' workshop.');

        return self::SUCCESS;
    }
}
