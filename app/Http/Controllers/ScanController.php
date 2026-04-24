<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    /**
     * Download or view a secure scan file
     */
    public function show($path)
    {
        // Path should start with 'documents_scans' or 'procurations'
        if (!preg_match('/^(documents_scans|procurations)\//', $path)) {
            abort(403, 'Unauthorized access.');
        }

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File not found.');
        }
        return response()->file(Storage::disk('local')->path($path));
    }
}
