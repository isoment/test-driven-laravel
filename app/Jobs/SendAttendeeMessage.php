<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AttendeeMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAttendeeMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public AttendeeMessage $attendeeMessage;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(AttendeeMessage $attendeeMessage)
    {
        $this->attendeeMessage = $attendeeMessage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
