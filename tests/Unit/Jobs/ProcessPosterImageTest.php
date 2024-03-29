<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessPosterImage;
use Database\Helpers\FactoryHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessPosterImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  @test
     */
    public function it_resizes_the_poster_image_to_600px_wide()
    {
        Storage::fake('public');

        Storage::disk('public')->put(
            'posters/example-poster.png', 
            file_get_contents(base_path('tests/__fixtures__/full-size-poster.png'))
        );

        $concert = FactoryHelpers::createUnpublished([
            'poster_image_path' => 'posters/example-poster.png'
        ]);

        ProcessPosterImage::dispatch($concert);

        // This is the image after it is processed
        $resizedImage = Storage::disk('public')->get('posters/example-poster.png');

        // Pull the width from the resized image and assign it to $width
        list($width, $height) = getimagesizefromstring($resizedImage);

        $this->assertEquals(600, $width);
        $this->assertEquals(776, $height);
    }

    /**
     *  @test
     */
    public function it_optimizes_the_poster_image()
    {
        Storage::fake('public');

        Storage::disk('public')->put(
            'posters/example-poster.png', 
            file_get_contents(base_path('tests/__fixtures__/small-unoptimized-poster.png'))
        );

        $concert = FactoryHelpers::createUnpublished([
            'poster_image_path' => 'posters/example-poster.png'
        ]);

        ProcessPosterImage::dispatch($concert);

        $optimizedImageSize = Storage::disk('public')->size('posters/example-poster.png');

        $originalSize = filesize(base_path('tests/__fixtures__/small-unoptimized-poster.png'));

        $this->assertLessThan($originalSize, $optimizedImageSize);

        $optimizedImageContents = Storage::disk('public')->get('posters/example-poster.png');
        $controlImageContents = file_get_contents(base_path('tests/__fixtures__/optimized-poster.png'));
        $this->assertEquals($controlImageContents, $optimizedImageContents);
    }
}
