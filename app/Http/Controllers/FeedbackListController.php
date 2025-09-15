<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class FeedbackListController extends Controller
{
    public function index(Application $application)
    {
        $user = Auth::user();
        if(!$user || $application->user_id !== $user->id){ abort(403); }
        $feedback = $application->feedback()->latest()->get()->map(fn($f)=>[
            'id'=>$f->id,
            'type'=>$f->type,
            'content'=>$f->content,
            'created_at'=>$f->created_at->format('Y-m-d H:i'),
        ]);
        return response()->json(['feedback'=>$feedback]);
    }
}
