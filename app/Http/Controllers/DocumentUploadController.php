<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentUploadController extends Controller
{
    public function index(Application $application)
    {
    $this->authorize('view', $application);
        $docs = $application->documents()->latest()->get()->map(fn($d)=>[
            'id'=>$d->id,
            'type'=>$d->type,
            'original_name'=>$d->original_name,
            'size_bytes'=>$d->size_bytes,
            'mime'=>$d->mime,
            'status'=>$d->status,
            'needs_translation'=>$d->needs_translation,
            'translation_status'=>$d->translation_status,
            'created_at'=>$d->created_at->toDateTimeString(),
        ]);
        return response()->json(['documents'=>$docs]);
    }

    public function store(Request $request, Application $application)
    {
    $this->authorize('uploadDocuments', $application);
        $data = $request->validate([
            'type' => ['required','string','max:60'],
            'file' => ['required','file','mimes:pdf,jpg,jpeg,png','max:5120'], // 5MB limit
            'needs_translation' => ['sometimes','boolean'],
        ]);
        $file = $data['file'];
        $folder = 'documents/'.$application->id;
        $ext = $file->getClientOriginalExtension();
        $filename = Str::uuid().($ext?'.'.$ext:'');
    // Store in private disk for security (configure 'private' disk in filesystems.php)
    $disk = config('filesystems.default') === 'public' && config('filesystems.disks.private') ? 'private' : config('filesystems.default');
    $path = $file->storeAs($folder,$filename,$disk);
        $doc = Document::create([
            'application_id' => $application->id,
            'type' => $data['type'],
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'size_bytes' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'status' => 'pending',
            'needs_translation' => $request->boolean('needs_translation'),
        ]);
        return response()->json([
            'id'=>$doc->id,
            'message'=>'Uploaded',
        ],201);
    }

    public function updateTranslationStatus(Request $request, Application $application, Document $document)
    {
        $this->authorize('translate', $document);
        if($document->application_id !== $application->id){
            return response()->json(['error'=>'Document mismatch'],422);
        }
        $data = $request->validate([
            'translation_status' => ['required','string','in:pending,in_progress,completed,rejected']
        ]);
        $document->update(['translation_status'=>$data['translation_status']]);
        return response()->json([
            'id'=>$document->id,
            'translation_status'=>$document->translation_status,
        ]);
    }

    // Ownership now enforced by ApplicationPolicy
}
