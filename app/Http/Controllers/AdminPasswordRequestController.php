<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentRequest;
use App\Models\Trainee;

class AdminPasswordRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentRequest::with('trainee.filiere')
            ->whereIn('document_type', ['Changement Mot de passe', 'Configuration Mot de passe']);

        // Search logic
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('trainee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('cef', 'like', "%{$search}%")
                  ->orWhere('cin', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderByRaw("CASE WHEN status = 'en_attente' THEN 1 ELSE 0 END DESC")
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Mark as read when admin visits the inbox (optional, we can use a separate flag or reuse the existing one)
        DocumentRequest::whereIn('document_type', ['Changement Mot de passe', 'Configuration Mot de passe'])
            ->where('is_read_by_admin', false)
            ->update(['is_read_by_admin' => true]);

        return view('admin.password_requests.index', compact('requests'));
    }

    public function approve(DocumentRequest $docRequest)
    {
        if (!in_array($docRequest->document_type, ['Changement Mot de passe', 'Configuration Mot de passe'])) {
            abort(403);
        }

        if ($docRequest->document_type === 'Configuration Mot de passe') {
            $docRequest->update(['status' => 'termine']);
            return back()->with('success', 'Notification marquée comme lue.');
        }

        // Reset trainee's password
        $trainee = $docRequest->trainee;
        $trainee->password = null;
        $trainee->save();

        $docRequest->update([
            'status' => 'termine'
        ]);

        return back()->with('success', 'Le mot de passe du stagiaire a été réinitialisé. Il pourra en créer un nouveau lors de sa prochaine connexion.');
    }

    public function reject(Request $request, DocumentRequest $docRequest)
    {
        if ($docRequest->document_type !== 'Changement Mot de passe') {
            abort(403);
        }

        $request->validate([
            'admin_message' => 'required|string'
        ]);

        $docRequest->update([
            'status' => 'rejete',
            'admin_message' => $request->admin_message,
        ]);

        return back()->with('error', 'La demande de changement de mot de passe a été rejetée.');
    }
}
