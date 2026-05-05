<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentReadyNotification;

class AdminRequestController extends Controller
{
    public function index()
    {
        $requests = DocumentRequest::with('trainee.filiere')
            ->whereNotIn('document_type', ['Changement Mot de passe', 'Configuration Mot de passe'])
            ->orderByRaw("CASE WHEN status = 'en_attente' THEN 1 ELSE 0 END DESC")
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Mark as read when admin visits the inbox
        DocumentRequest::where('is_read_by_admin', false)
            ->whereNotIn('document_type', ['Changement Mot de passe', 'Configuration Mot de passe'])
            ->update(['is_read_by_admin' => true]);

        return view('admin.requests.index', compact('requests'));
    }

    public function schedule(Request $request, DocumentRequest $docRequest)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'admin_message' => 'nullable|string'
        ]);

        $docRequest->update([
            'status' => 'planifie',
            'appointment_date' => $request->appointment_date,
            'admin_message' => $request->admin_message,
        ]);

        $email = $docRequest->trainee->cef . '@ofppt-edu.ma';
        Mail::to($email)->send(new DocumentReadyNotification($docRequest));

        return back()->with('success', 'Le rendez-vous a été fixé et envoyé au stagiaire.');
    }

    public function complete(DocumentRequest $docRequest)
    {
        $docRequest->update([
            'status' => 'termine'
        ]);

        $email = $docRequest->trainee->cef . '@ofppt-edu.ma';
        Mail::to($email)->send(new DocumentReadyNotification($docRequest));

        return back()->with('success', 'La demande a été marquée comme terminée.');
    }

    public function reject(Request $request, DocumentRequest $docRequest)
    {
        $request->validate([
            'admin_message' => 'required|string'
        ]);

        $docRequest->update([
            'status' => 'rejete',
            'admin_message' => $request->admin_message,
        ]);

        $email = $docRequest->trainee->cef . '@ofppt-edu.ma';
        Mail::to($email)->send(new DocumentReadyNotification($docRequest));

        return back()->with('error', 'La demande a été rejetée.');
    }

    public function checkNew()
    {
        $newCount = DocumentRequest::where('is_read_by_admin', false)
            ->whereNotIn('document_type', ['Changement Mot de passe', 'Configuration Mot de passe'])
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        return response()->json([
            'has_new' => $newCount > 0,
            'count' => $newCount
        ]);
    }
}
