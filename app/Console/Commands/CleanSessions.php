<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class CleanSessions extends Command
{
    protected $signature = 'sessions:clean';
    protected $description = 'Clean old session files';

    public function handle()
    {
        $sessionPath = storage_path('framework/sessions');
        $files = File::files($sessionPath);
        $now = Carbon::now();
        $count = 0;

        foreach ($files as $file) {
            // Delete files older than 24 hours
            if ($now->diffInHours(Carbon::createFromTimestamp($file->getMTime())) > 24) {
                File::delete($file);
                $count++;
            }
        }

        $this->info("Cleaned {$count} old session files.");
    }
} 