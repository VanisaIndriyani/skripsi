<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class DownloadFaceModels extends Command
{
    protected $signature = 'pms:download-models';
    protected $description = 'Download face-api.js models to public/models';

    public function handle()
    {
        $baseUrl = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/';
        $files = [
            'tiny_face_detector_model-weights_manifest.json',
            'tiny_face_detector_model-shard1',
            'face_landmark_68_model-weights_manifest.json',
            'face_landmark_68_model-shard1',
            'face_recognition_model-weights_manifest.json',
            'face_recognition_model-shard1',
            'face_recognition_model-shard2',
            'ssd_mobilenetv1_model-weights_manifest.json',
            'ssd_mobilenetv1_model-shard1',
            'ssd_mobilenetv1_model-shard2',
        ];

        $targetDir = public_path('models');
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        foreach ($files as $file) {
            $this->info("Downloading {$file}...");
            $response = Http::get($baseUrl . $file);
            
            if ($response->successful()) {
                File::put($targetDir . '/' . $file, $response->body());
                $this->info("✓ Saved to public/models/{$file}");
            } else {
                $this->error("✗ Failed to download {$file}");
            }
        }

        $this->info('All models downloaded successfully!');
    }
}
