<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SupabaseStorage
{
    public static function upload($file, $filename)
    {
        $bucket = env('SUPABASE_BUCKET');
        $url = env('SUPABASE_URL') . "/storage/v1/object/$bucket/$filename";

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
            'Content-Type' => $file->getMimeType()
        ])->put($url, file_get_contents($file));

        return $response->successful();
    }

    public static function getPublicUrl($filename)
    {
        return env('SUPABASE_URL') . "/storage/v1/object/public/" . env('SUPABASE_BUCKET') . "/$filename";
    }
}
