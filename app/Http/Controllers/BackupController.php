<?php

namespace App\Http\Controllers;

use App\Models\BackupLog;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function download(BackupLog $backup): StreamedResponse
    {
        abort_unless($backup->status === 'verified' && Storage::disk('local')->exists($backup->path), 404);
        abort_unless(Storage::disk('local')->size($backup->path) === (int) $backup->file_size, 409);
        return Storage::disk('local')->download($backup->path, $backup->file_name, ['Content-Type'=>'application/json']);
    }
}
