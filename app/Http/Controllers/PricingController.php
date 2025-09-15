<?php
namespace App\Http\Controllers;

use App\Models\VisaCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PricingController extends Controller
{
    public function index(Request $request)
    {
        // Prefer explicitly stored visa_type (set when a terminal actionable outcome was reached)
        $visaType = session('quiz.visa_type');

        // Direct override via query (?vt=I130 or ?t=RELATIVE_I130) for deep-links
        if ($request->filled('vt')) {
            $candidate = preg_replace('/[^A-Z0-9_]/i','', $request->query('vt'));
            if ($candidate) {
                $visaType = strtoupper($candidate);
                session(['quiz.visa_type' => $visaType]);
            }
        } elseif ($request->filled('t')) {
            $terminal = preg_replace('/[^A-Z0-9_]/i','', $request->query('t'));
            if ($terminal) {
                $mapped = \App\Services\VisaTypeMapper::map($terminal);
                if ($mapped) {
                    $visaType = $mapped;
                    session(['quiz.visa_type' => $visaType]);
                }
            }
        }

        // Backward compatibility: if not set yet but quiz.current holds a terminal code, attempt mapping
        if (!$visaType && session('quiz.current') && is_string(session('quiz.current'))) {
            $visaType = \App\Services\VisaTypeMapper::map(session('quiz.current'));
            if ($visaType) {
                session(['quiz.visa_type' => $visaType]); // cache for subsequent requests
            }
        }

        $hasVisaTypeColumn = Schema::hasColumn('packages','visa_type');

        // Map visa_type to category ids (adjust as needed)
        $visaTypeCategoryMap = [
            'I485' => 1, // Marriage Green Card inside US (example)
            'I485_PARENT' => 2, // Parent Adjustment inside US
            'I485_CHILD' => 2,  // Child Adjustment inside US
            'I130' => 3, // Spouse outside (example)
            'I130_PARENT' => 4, // Parent abroad consular petition
            'I130_CHILD' => 4,  // Child abroad consular petition
            'I130_SIBLING' => 4,  // Sibling abroad consular petition
            'K1'   => 5,
            'I751' => 6,
            'I90'  => 7,
            'DACA' => 8,
            'N400' => 9,
        ];

        if ($visaType && $hasVisaTypeColumn) {
            $targetCategoryId = $visaTypeCategoryMap[$visaType] ?? null;
            // Strict filtering: only packages that explicitly match this visa type.
            $visaCategoriesQuery = VisaCategory::query();
            if ($targetCategoryId) {
                $visaCategoriesQuery->where('id',$targetCategoryId);
            } else {
                $visaCategoriesQuery->whereHas('packages', function($q) use ($visaType) {
                    $q->where('active',true)->where('visa_type',$visaType);
                });
            }
            $visaCategories = $visaCategoriesQuery->with(['packages' => function($q) use ($visaType) {
                $q->where('active', true)
                   ->where('visa_type',$visaType)
                   ->orderBy('price_cents','ASC');
            }])->get();

            $globalPackages = collect(); // hide uncategorized for strict per-visa view
        } else {
            $visaCategories = VisaCategory::with(['packages' => function($q) {
                $q->where('active', true)->orderBy('price_cents','ASC');
            }])->get();
            $globalPackages = \App\Models\Package::query()
                ->whereNull('visa_category_id')
                ->where('active',true)
                ->orderBy('price_cents','ASC')
                ->get();
        }

        $totalShown = ($globalPackages?->count() ?? 0) + $visaCategories->sum(fn($c)=>$c->packages->count());

        return view('pricing', [
            'visaCategories' => $visaCategories,
            'visaType' => $visaType,
            'globalPackages' => $globalPackages,
            'sourceTerminal' => $request->query('t'),
            'totalShown' => $totalShown
        ]);
    }
}
