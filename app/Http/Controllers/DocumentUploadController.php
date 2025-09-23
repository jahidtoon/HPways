<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class DocumentUploadController extends Controller
{
    public function index(Application $application)
    {
        $this->authorize('view', $application);
        // Serve JSON for API/AJAX clients; otherwise redirect to the Upload Documents UI.
        if (request()->wantsJson() || str_contains(request()->header('Accept',''), 'application/json')) {
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
    // Build required/optional like ApplicantController::uploadDocuments and render the same Blade
        $currentApplication = $application->loadMissing('selectedPackage');
        $uploadedTypes = $currentApplication->documents()->pluck('type')->filter()->map(fn($t)=>strtoupper($t))->unique();
        $required = [];
        $optional = [];

        // Package-specific required docs first
        if (Schema::hasTable('package_required_documents') && $currentApplication->selectedPackage) {
            try {
                $rows = $currentApplication->selectedPackage->requiredDocuments()->where('active',true)->get();
                foreach($rows as $r){
                    $item = [
                        'code'=>strtoupper($r->code),
                        'label'=>$r->label,
                        'required'=>(bool)$r->required,
                        'translation_possible'=>(bool)($r->translation_possible ?? false),
                        'uploaded'=>$uploadedTypes->contains(strtoupper($r->code)),
                    ];
                    if ($r->required) { $required[] = $item; } else { $optional[] = $item; }
                }
            } catch (\Throwable $e) { /* ignore and fall back */ }
        }

        // Visa-type fallback (DB or config)
        if (empty($required) && empty($optional)) {
            $visa = $currentApplication->visa_type;
            $rows = collect();
            if ($visa && Schema::hasTable('required_documents')) {
                try {
                    $rows = \App\Models\RequiredDocument::where('visa_type',$visa)->where('active',true)->get()
                        ->map(fn($r)=>[
                            'code'=>strtoupper($r->code),
                            'label'=>$r->label,
                            'required'=>(bool)$r->required,
                            'translation_possible'=>(bool)($r->translation_possible ?? false),
                        ]);
                } catch (\Throwable $e) { $rows = collect(); }
            }
            if ($rows->isEmpty() && $visa) {
                $rows = collect(config('required_documents.'.strtoupper($visa), []));
            }
            foreach($rows as $r){
                $code = strtoupper($r['code'] ?? '');
                if(!$code) continue;
                $item = [
                    'code'=>$code,
                    'label'=>$r['label'] ?? $code,
                    'required'=>(bool)($r['required'] ?? false),
                    'translation_possible'=>(bool)($r['translation_possible'] ?? false),
                    'uploaded'=>$uploadedTypes->contains($code),
                ];
                if ($item['required']) { $required[] = $item; } else { $optional[] = $item; }
            }
        }

        return view('dashboard.applicant.upload-documents', compact('currentApplication','required','optional'));
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
        // If the client expects JSON (AJAX/API), return JSON; otherwise redirect back to the upload UI with a flash message
        if ($request->wantsJson() || str_contains($request->header('Accept',''), 'application/json')) {
            return response()->json([
                'id'=>$doc->id,
                'message'=>'Uploaded',
            ],201);
        }
        return redirect()
            ->route('applications.documents.index', $application->id)
            ->with('success', 'Document uploaded successfully.');
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
