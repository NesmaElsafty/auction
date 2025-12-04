<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use App\Models\SocialMedia;
use App\Http\Controllers\Controller;
use App\Http\Resources\ContactUsResource;
use App\Http\Resources\SocialMediaResource;
use Illuminate\Http\Request;


class ContactUsController extends Controller
{

    public function index(Request $request)
    {
        try {
            $contactUs = ContactUs::first();
            $socialMedia = SocialMedia::all();
            return response()->json([
                'success' => true,
                'message' => 'Contact us and social media retrieved successfully',
                'data' => [
                    'contact_us' => $contactUs ? new ContactUsResource($contactUs) : null,
                    'social_media' => SocialMediaResource::collection($socialMedia),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contact us and social media',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {

           $request->validate([
                'phone' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'copyright' => 'nullable|string|max:255',
                'social_media' => 'nullable|array',
                'social_media.*.platform' => 'nullable|string|max:255',
                'social_media.*.link' => 'nullable|string|max:255',
            ]);
            $contactUs = ContactUs::updateOrCreate([
                'id' => 1,
            ], [
                'phone' => $request->phone,
                'email' => $request->email,
                'copyright' => $request->copyright,
            ]);

            foreach ($request->social_media as $socialMediaData) {
                SocialMedia::updateOrCreate([
                    'platform' => $socialMediaData['platform'],
                ], [
                    'link' => $socialMediaData['link'],
                ]);
            }

            $socialMedia = SocialMedia::get();

            return response()->json([
                'success' => true,
                'message' => 'Contact us updated successfully',
                'data' => ['contact_us' => new ContactUsResource($contactUs), 
                'social_media' => SocialMediaResource::collection($socialMedia)],

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update contact us',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}