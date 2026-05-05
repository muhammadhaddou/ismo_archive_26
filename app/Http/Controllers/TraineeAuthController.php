<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trainee;
use App\Models\DocumentRequest;

class TraineeAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('portal.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'cef' => 'required',
            'cin' => 'required'
        ]);

        $trainee = Trainee::where('cef', trim($request->cef))
            ->first();

        if ($trainee) {
            // S'il n'a pas encore de mot de passe, on utilise la CIN comme mot de passe provisoire
            if (empty($trainee->password)) {
                if (trim($request->cin) === $trainee->cin) {
                    $request->session()->put('trainee_id', $trainee->id);
                    return redirect()->route('trainee.dashboard');
                }
            } else {
                // S'il a déjà un mot de passe configuré, on le vérifie
                if (\Illuminate\Support\Facades\Hash::check($request->cin, $trainee->password)) {
                    $request->session()->put('trainee_id', $trainee->id);
                    return redirect()->route('trainee.dashboard');
                }
            }
        }

        return back()->with('error', 'Informations incorrectes. Veuillez vérifier vos accès.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('trainee_id');
        return redirect()->route('trainee.login');
    }

    public function showPasswordSetupForm(Request $request)
    {
        // Ensure trainee is in session but has no password yet
        if (!$request->session()->has('trainee_id')) {
            return redirect()->route('trainee.login');
        }

        $trainee = Trainee::find($request->session()->get('trainee_id'));
        if (!$trainee || !empty($trainee->password)) {
            return redirect()->route('trainee.dashboard');
        }

        return view('portal.password_setup');
    }

    public function setupPassword(Request $request)
    {
        if (!$request->session()->has('trainee_id')) {
            return redirect()->route('trainee.login');
        }

        $trainee = Trainee::find($request->session()->get('trainee_id'));
        if (!$trainee || !empty($trainee->password)) {
            return redirect()->route('trainee.dashboard');
        }

        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $trainee->password = bcrypt($request->password);
        $trainee->save();

        DocumentRequest::create([
            'trainee_id' => $trainee->id,
            'document_type' => 'Configuration Mot de passe',
            'status' => 'en_attente',
            'admin_message' => $request->password,
        ]);

        return redirect()->route('trainee.dashboard')->with('success', 'Votre mot de passe a été configuré avec succès !');
    }

    public function requestPasswordReset(Request $request)
    {
        $request->validate([
            'cef' => 'required',
            'date_naissance' => 'required|date'
        ]);

        $trainee = Trainee::where('cef', trim($request->cef))
            ->whereDate('date_naissance', $request->date_naissance)
            ->first();

        if (!$trainee) {
            return back()->with('error', 'Aucun stagiaire trouvé avec ces informations.');
        }

        // Check if there's already a pending password reset request
        $existing = DocumentRequest::where('trainee_id', $trainee->id)
            ->where('document_type', 'Changement Mot de passe')
            ->where('status', 'en_attente')
            ->first();

        if ($existing) {
            return back()->with('error', 'Vous avez déjà une demande de réinitialisation en cours de traitement.');
        }

        DocumentRequest::create([
            'trainee_id' => $trainee->id,
            'document_type' => 'Changement Mot de passe',
            'status' => 'en_attente',
        ]);

        return back()->with('success', 'Votre demande de réinitialisation a été envoyée à l\'administration avec succès.');
    }
}
