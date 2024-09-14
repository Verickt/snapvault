<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use App\Models\Listing;

interface ImageServiceInterface
{
    /**
     * Store an image and return the path.
     *
     * @param UploadedFile $image
     * @param string $directory
     * @return string
     */
    public function storeImage(UploadedFile $image, string $directory): string;

    /**
     * Delete a single image.
     *
     * @param string $path
     * @return bool
     */
    public function deleteImage(string $path): bool;

    /**
     * Delete multiple images.
     *
     * @param array $paths
     * @return void
     */
    public function deleteImages(array $paths): void;

    /**
     * Delete all images associated with a listing.
     *
     * @param Listing $listing
     * @return void
     */
    public function deleteListingImages(Listing $listing): void;
}
