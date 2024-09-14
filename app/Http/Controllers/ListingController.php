<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListingRequest;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use App\Services\ChatGPTServiceInterface;
use App\Services\ImageServiceInterface;
use App\Services\VisionAPIServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ImageServiceInterface $imageService,
        protected VisionAPIServiceInterface $visionAPIService,
        protected ChatGPTServiceInterface $chatGPTService
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Listing::class);
        return Inertia::render('Listings/Index', [
            'listings' => ListingResource::collection(Listing::all()),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Listing::class);
        return Inertia::render('Listings/Create');
    }

    public function store(ListingRequest $request)
    {
        $this->authorize('create', Listing::class);
        $listing = Listing::create();

        if ($request->hasFile('images')) {
            $this->handleImageUpload($request->file('images'), $listing);
        }

        return Inertia::render('Listings/Edit', [
            'listing' => new ListingResource($listing),
            'message' => 'Images uploaded successfully. Please add title and description.',
        ]);
    }
    public function show(Listing $listing)
    {
        $this->authorize('view', $listing);
        return Inertia::render('Listings/Show', [
            'listing' => new ListingResource($listing),
        ]);
    }

    public function edit(Listing $listing)
    {
        $this->authorize('update', $listing);
        return Inertia::render('Listings/Edit', [
            'listing' => new ListingResource($listing),
        ]);
    }

    public function update(ListingRequest $request, Listing $listing)
    {
        $this->authorize('update', $listing);
        $validated = $request->validated();
        $listing->update($validated);

        if ($request->hasFile('images')) {
            $this->handleImageUpload($request->file('images'), $listing);
        }

        if ($request->has('deleted_images')) {
            $this->handleImageDeletion($request->input('deleted_images'), $listing);
        }

        return redirect()->route('listings.show', $listing->id)
            ->with('success', 'Listing updated successfully.');
    }

    public function destroy(Listing $listing)
    {
        $this->authorize('delete', $listing);
        $this->imageService->deleteListingImages($listing);
        $listing->delete();

        return redirect()->route('listings.index')->with('success', 'Listing deleted successfully.');
    }

    protected function handleImageUpload($images, Listing $listing)
    {
        $imagePaths = $listing->image_paths ?? [];

        foreach ($images as $image) {
            $path = $this->imageService->storeImage($image, 'listing-images');
            $imagePaths[] = $path;
        }

        $listing->update(['image_paths' => $imagePaths]);
    }

    protected function handleImageDeletion($deletedImages, Listing $listing)
    {
        $currentImages = $listing->image_paths ?? [];
        $updatedImages = array_diff($currentImages, $deletedImages);

        foreach ($deletedImages as $image) {
            $this->imageService->deleteImage($image);
        }

        $listing->update(['image_paths' => array_values($updatedImages)]);
    }

    public function generateTitle(Request $request, Listing $listing)
    {
        $this->authorize('update', $listing);

        $imagePaths = $request->input('images');
        if (!$imagePaths || !is_array($imagePaths) || empty($imagePaths)) {
            return response()->json(['error' => 'Invalid image paths provided.'], 400);
        }

        $categories = [];
        $dominantColors = [];
        $textSegments = [];

        foreach ($imagePaths as $imagePath) {
            if (Storage::disk('public')->exists($imagePath)) {
                $analysisResult = $this->visionAPIService->analyzeImage(Storage::disk('public')->path($imagePath));
                ray($analysisResult);

                if (isset($analysisResult['categories']) && is_array($analysisResult['categories'])) {
                    $categories = array_merge($categories, $analysisResult['categories']);
                }

                if (isset($analysisResult['colors']) && is_array($analysisResult['colors'])) {
                    $dominantColors = array_merge($dominantColors, $analysisResult['colors']);
                }

                if (!empty($analysisResult['title'])) {
                    $textSegments[] = $analysisResult['title'];
                }
            }
        }

        $categories = array_unique($categories);
        $dominantColors = array_unique($dominantColors);

        $prompt = "Create a descriptive title for an item based on the following characteristics:\n";
        if (!empty($categories)) {
            $prompt .= "Categories: " . implode(', ', array_slice($categories, 0, 5)) . ".\n";
        }
        if (!empty($dominantColors)) {
            $prompt .= "Colors: " . implode(', ', array_slice($dominantColors, 0, 3)) . ".\n";
        }
        if (!empty($textSegments)) {
            $prompt .= "Detected text: " . implode(' ', array_slice($textSegments, 0, 1)) . ".\n";
        }
        $prompt .= "Use this information to generate a concise and descriptive title for the item.";

        // Use the ChatGPT service
        $generatedTitle = $this->chatGPTService->generateResponse($prompt);

        return response()->json([
            'title' => $generatedTitle,
        ]);
    }

    public function generateDescription(Request $request, Listing $listing)
    {
        $this->authorize('update', $listing);

        $imagePaths = $request->input('images');
        if (!$imagePaths || !is_array($imagePaths) || empty($imagePaths)) {
            return response()->json(['error' => 'Invalid image paths provided.'], 400);
        }

        $categories = [];
        $dominantColors = [];
        $textSegments = [];

        foreach ($imagePaths as $imagePath) {
            if (Storage::disk('public')->exists($imagePath)) {
                $analysisResult = $this->visionAPIService->analyzeImage(Storage::disk('public')->path($imagePath));

                if (isset($analysisResult['categories']) && is_array($analysisResult['categories'])) {
                    $categories = array_merge($categories, $analysisResult['categories']);
                }

                if (isset($analysisResult['colors']) && is_array($analysisResult['colors'])) {
                    $dominantColors = array_merge($dominantColors, $analysisResult['colors']);
                }

                if (!empty($analysisResult['description'])) {
                    $textSegments[] = $analysisResult['description'];
                }
            }
        }

        $categories = array_unique($categories);
        $dominantColors = array_unique($dominantColors);

        $prompt = "Create a detailed and descriptive paragraph for an item based on the following characteristics:\n";
        if (!empty($categories)) {
            $prompt .= "Categories: " . implode(', ', array_slice($categories, 0, 5)) . ".\n";
        }
        if (!empty($dominantColors)) {
            $prompt .= "Colors: " . implode(', ', array_slice($dominantColors, 0, 3)) . ".\n";
        }
        if (!empty($textSegments)) {
            $prompt .= "Detected text: " . implode(' ', array_slice($textSegments, 0, 1)) . ".\n";
        }
        $prompt .= "Provide a comprehensive and engaging description for this item.";

        // Use the ChatGPT service
        $generatedDescription = $this->chatGPTService->generateResponse($prompt);

        return response()->json([
            'description' => $generatedDescription,
        ]);
    }

}
