<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function download(Document $document)
    {
        // Authorize that the current user can download this document.
        // For example, only reviewers should be able to download.
        // You'll need to implement your own authorization logic here.
        // For now, let's assume any authenticated user can download.
        // if (!auth()->user()->can('download', $document)) {
        //     abort(403, 'Unauthorized action.');
        // }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        $headers = [
            'Content-Type' => $document->mime_type,
            'Content-Disposition' => 'attachment; filename="' . $document->file_name . '"',
        ];

        return Storage::disk('public')->download($document->file_path, $document->file_name, $headers);
    }
}
