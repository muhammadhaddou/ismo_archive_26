<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trainee;
use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Hash;

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

        $trainee = Trainee::query()->where('cef', '=', trim($request->cef))->first();

        if (!$trainee) {
            return back()->with('error', 'Informations incorrectes. Veuillez vérifier vos accès.');
        }

        $inputPassword = trim($request->cin);
        $dbCin = trim($trainee->cin);
        
        $isAuthenticated = false;
        $isFirstLogin = false;

        if (empty($trainee->password)) {
            // Cas 1 : Aucun mot de passe → CIN comme accès provisoire (insensible à la casse)
            if (strcasecmp($inputPassword, $dbCin) === 0) {
                $isAuthenticated = true;
                $isFirstLogin = true;
            }
        } else {
            // Cas 2 : Mot de passe configuré → on vérifie s'il est hashé ou non
            $info = Hash::info($trainee->password);
            $isHashed = $info['algoName'] !== 'unknown';
            
            if ($isHashed) {
                if (Hash::check($inputPassword, $trainee->password)) {
                    $isAuthenticated = true;
                    // Si le mot de passe est encore le CIN par défaut → forcer changement
                    if (Hash::check($dbCin, $trainee->password) || 
                        Hash::check(strtolower($dbCin), $trainee->password) || 
                        Hash::check(strtoupper($dbCin), $trainee->password)) {
                        $isFirstLogin = true;
                    }
                }
            } else {
                // Le mot de passe n'est pas hashé (cas d'une importation directe ou ancienne DB)
                if ($inputPassword === $trainee->password || strcasecmp($inputPassword, $dbCin) === 0) {
                    $isAuthenticated = true;
                    $isFirstLogin = true; // Forcer le hashage via setup
                }
            }
        }

        if ($isAuthenticated) {
            $request->session()->put('trainee_id', $trainee->id);
            
            if ($isFirstLogin) {
                $request->session()->put('first_login', true);
                return redirect()->route('trainee.password.setup');
            }
            
            return redirect()->route('trainee.dashboard');
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
        if (!$request->session()->has('trainee_id')) {
            return redirect()->route('trainee.login');
        }

        $trainee = Trainee::query()->find($request->session()->get('trainee_id'));
        if (!$trainee) {
            return redirect()->route('trainee.login');
        }

        return view('portal.password_setup', compact('trainee'));
    }

    public function setupPassword(Request $request)
    {
        if (!$request->session()->has('trainee_id')) {
            return redirect()->route('trainee.login');
        }

        $trainee = Trainee::query()->find($request->session()->get('trainee_id'));
        if (!$trainee) {
            return redirect()->route('trainee.login');
        }

        $request->validate([
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        // Sauvegarder le nouveau mot de passe
        $trainee->password = bcrypt($request->password);
        $trainee->save();

        // Notifier l'administration (visible dans /admin/password-requests)
        DocumentRequest::create([
            'trainee_id'    => $trainee->id,
            'document_type' => 'Activation Compte',
            'status'        => 'en_attente',
            'admin_message' => $request->password,
        ]);

        $request->session()->forget('first_login');

        return redirect()->route('trainee.dashboard')
            ->with('success', 'Bienvenue ! Votre mot de passe a été configuré avec succès. 🎉');
    }

    public function requestPasswordReset(Request $request)
    {
        $request->validate([
            'cef'           => 'required',
            'date_naissance' => 'required|date'
        ]);

        $trainee = Trainee::query()->where('cef', '=', trim($request->cef))
            ->whereDate('date_naissance', '=', $request->date_naissance)
            ->first();

        if (!$trainee) {
            return back()->with('error', 'Aucun stagiaire trouvé avec ces informations.');
        }

        $existing = DocumentRequest::query()->where('trainee_id', '=', $trainee->id)
            ->where('document_type', 'Changement Mot de passe')
            ->where('status', 'en_attente')
            ->first();

        if ($existing) {
            return back()->with('error', 'Vous avez déjà une demande de réinitialisation en cours de traitement.');
        }

        DocumentRequest::create([
            'trainee_id'    => $trainee->id,
            'document_type' => 'Changement Mot de passe',
            'status'        => 'en_attente',
        ]);

        return back()->with('success', 'Votre demande de réinitialisation a été envoyée à l\'administration avec succès.');
    }
}
