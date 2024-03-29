<?php

namespace App\Jobs;

use App\Models\Concert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProcessPosterImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Concert $concert;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Concert $concert)
    {
        $this->concert = $concert;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $imageContents = Storage::disk('public')->get($this->concert->poster_image_path);

        // Bring the image data into Intervention
        $image = Image::make($imageContents);

        $image->resize(600, null, function($constraint) {
            $constraint->aspectRatio();
        })->limitColors(255)->encode();

        Storage::disk('public')->put($this->concert->poster_image_path, (string) $image);
    }
}
