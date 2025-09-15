<?php
namespace App\Services;

use App\Models\Application;
use App\Models\RequiredDocument;
use App\Models\Package;

class RequiredDocumentService
{
    /**
     * Determine which required documents are still missing for a given application.
     * Assumes Application has documents() relation with type matching required code or label mapping logic.
     */
    public function missingFor(Application $application): array
    {
        $visa = $application->visa_type;
        if(!$visa) return [];
        $query = RequiredDocument::where('visa_type',$visa)->where('active',true);

        // Optional: adjust requirements by package tier (e.g., premium includes translation docs)
        $pkg = $application->selectedPackage; // relation loaded optionally
        $tier = $pkg ? strtolower($pkg->name) : null; // assuming name Basic/Advanced/Premium
        if($tier === 'premium') {
            // For premium, still require all; potential future logic
        }

        $required = $query->get();
        if($required->isEmpty()) return [];
        $uploadedTypes = $application->documents()->pluck('type')->map(fn($t)=>strtoupper($t))->all();
        $missing = [];
        foreach($required as $doc){
            if(!$doc->required) continue; // optional skip from missing list
            $codeUpper = strtoupper($doc->code);
            if(!in_array($codeUpper,$uploadedTypes)){
                $missing[] = [
                    'code'=>$doc->code,
                    'label'=>$doc->label,
                    'translation_possible'=>$doc->translation_possible,
                ];
            }
        }
        return $missing;
    }

    /**
     * Compute a richer progress structure: documents, payment, overall.
     */
    public function progressBreakdown(Application $application): array
    {
        $visa = $application->visa_type;
        $docsReq = RequiredDocument::where('visa_type',$visa)->where('active',true)->where('required',true)->count();
        $uploadedRequired = 0;
        if($docsReq){
            $uploadedRequired = $application->documents()
                ->whereIn('type', RequiredDocument::where('visa_type',$visa)->where('active',true)->where('required',true)->pluck('code'))
                ->count();
        }
        $docPct = $docsReq ? (int) floor(($uploadedRequired / $docsReq) * 100) : 0;
        $payPct = ($application->payment_status === 'paid') ? 100 : 0;
        // Weighted formula: status progress base + docs 30% + payment 20%
        $overall = min(100, (int) round(($application->progress_pct * 0.5) + ($docPct * 0.3) + ($payPct * 0.2)));
        return [
            'documents_pct' => $docPct,
            'payment_pct' => $payPct,
            'status_pct' => $application->progress_pct,
            'overall_pct' => $overall,
        ];
    }
}
