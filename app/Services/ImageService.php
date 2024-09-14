<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Listing;

class ImageService implements ImageServiceInterface
{
    /**
     * Store an image and return the path.
     *
     * @param UploadedFile $image
     * @param string $directory
     * @return string
     */
    public function storeImage(UploadedFile $image, string $directory): string
    {
        return $image->store($directory, 'public');
    }

    /**
     * Delete a single image.
     *
     * @param string $path
     * @return bool
     */
    public function deleteImage(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    /**
     * Delete multiple images.
     *
     * @param array $paths
     * @return void
     */
    public function deleteImages(array $paths): void
    {
        foreach ($paths as $path) {
            $this->deleteImage($path);
        }
    }

    /**
     * Delete all images associated with a listing.
     *
     * @param Listing $listing
     * @return void
     */
    public function deleteListingImages(Listing $listing): void
    {
        // Check if image_paths is an array
        $imagePaths = is_array($listing->image_paths)
            ? $listing->image_paths
            : json_decode($listing->image_paths, true); // Decode JSON string to an array

        // Ensure $imagePaths is an array before proceeding
        if (is_array($imagePaths)) {
            $this->deleteImages($imagePaths);
        }
    }

}
