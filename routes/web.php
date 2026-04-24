<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TraineeController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\SecteurController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\DiplomesPrêtsController;
use App\Http\Controllers\FiliereStatsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirection vers dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});


// Routes protégées (auth)
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Import Excel
    Route::get('trainees/import',  [ImportController::class, 'index'])->name('trainees.import');
    Route::post('trainees/import', [ImportController::class, 'store'])->name('trainees.import.store');

    // Trainees
    Route::resource('trainees', TraineeController::class);
    Route::post('trainees/{trainee}/promouvoir', [TraineeController::class, 'promouvoir'])->name('trainees.promouvoir');


    // Documents static routes — كلها قبل resource
    Route::get('documents/bac',              [DocumentController::class, 'index'])->name('documents.bac')->defaults('type', 'Bac');
    Route::get('documents/bac/temp-out',     [DocumentController::class, 'tempOut'])->name('documents.bac.temp-out');
    Route::get('documents/bac/ecoule',       [DocumentController::class, 'ecoule'])->name('documents.bac.ecoule');
    Route::get('documents/bac/final-out',    [DocumentController::class, 'finalOut'])->name('documents.bac.final-out');
    Route::get('documents/diplome',          [DocumentController::class, 'index'])->name('documents.diplome')->defaults('type', 'Diplome');
    Route::get('documents/diplome/prets',    [DocumentController::class, 'prets'])->name('documents.diplome.prets');
    Route::get('documents/bulletin',         [DocumentController::class, 'index'])->name('documents.bulletin')->defaults('type', 'Bulletin');
    Route::get('documents/attestation',      [DocumentController::class, 'index'])->name('documents.attestation')->defaults('type', 'Attestation');

    Route::resource('documents', DocumentController::class)->only(['index', 'create', 'store', 'show']);

    Route::post('documents/{document}/sortie', [DocumentController::class, 'sortie'])->name('documents.sortie');
    Route::post('documents/{document}/retour', [DocumentController::class, 'retour'])->name('documents.retour');
    Route::post('documents/{document}/scan',   [DocumentController::class, 'uploadScan'])->name('documents.scan');
    Route::get('scans/{path}',                 [\App\Http\Controllers\ScanController::class, 'show'])->where('path', '.*')->name('scans.show');

    // Movements
    Route::get('movements',       [MovementController::class, 'index'])->name('movements.index');
    Route::get('movements/today', [MovementController::class, 'today'])->name('movements.today');

    // ✅ Calendrier (المهم)
    Route::get('calendrier', [CalendrierController::class, 'index'])->name('calendrier');

    // Validations
    Route::get('validations', [ValidationController::class, 'index'])->name('validations.index');
    Route::get('trainees/{trainee}/validation/create', [ValidationController::class, 'create'])->name('validations.create');
    Route::post('trainees/{trainee}/validation', [ValidationController::class, 'store'])->name('validations.store');
    Route::get('trainees/{trainee}/validation', [ValidationController::class, 'show'])->name('validations.show');
    Route::delete('validations/{validation}', [ValidationController::class, 'destroy'])->name('validations.destroy');

    // Users
    Route::resource('users', UserController::class)->except(['show']);

    // Secteurs & Filieres
    Route::resource('secteurs', SecteurController::class)->except(['show']);
    Route::resource('filieres', FiliereController::class)->except(['show']);

    // Search global
    Route::get('search', [SearchController::class, 'index'])->name('search');

    // API (filtres dynamiques)
    Route::prefix('api')->group(function () {

        Route::get('/filiere/{filiere}/groups', function (\App\Models\Filiere $filiere) {
            return response()->json([
                'groups' => \App\Models\Trainee::where('filiere_id', $filiere->id)
                    ->distinct()
                    ->pluck('group')
                    ->sort()
                    ->values()
            ]);
        });

        Route::get('/filiere/{filiere}/years', function (\App\Models\Filiere $filiere) {
            return response()->json([
                'years' => \App\Models\Trainee::where('filiere_id', $filiere->id)
                    ->distinct()
                    ->pluck('graduation_year')
                    ->sortDesc()
                    ->values()
            ]);
        });

    });

    // Diplômes prêts — gestion remise et signature
    Route::get('diplomes-prets',                              [DiplomesPrêtsController::class, 'index'])->name('diplomes.prets');
    Route::post('/diplomes-prets/{trainee}/check-promote',   [DiplomesPrêtsController::class, 'checkAndPromote'])->name('diplomes.checkPromote');
    Route::post('/diplomes-prets/{trainee}/signature',       [DiplomesPrêtsController::class, 'saveSignature'])->name('diplomes.saveSignature');

    // Admin Trainee Requests and Settings
    Route::get('admin/requests', [App\Http\Controllers\AdminRequestController::class, 'index'])->name('admin.requests.index');
    Route::post('admin/requests/{docRequest}/schedule', [App\Http\Controllers\AdminRequestController::class, 'schedule'])->name('admin.requests.schedule');
    Route::post('admin/requests/{docRequest}/complete', [App\Http\Controllers\AdminRequestController::class, 'complete'])->name('admin.requests.complete');
    Route::post('admin/requests/{docRequest}/reject', [App\Http\Controllers\AdminRequestController::class, 'reject'])->name('admin.requests.reject');
    Route::get('api/check-new-requests', [App\Http\Controllers\AdminRequestController::class, 'checkNew']);
    Route::get('admin/settings/availability', [App\Http\Controllers\AdminSettingsController::class, 'index'])->name('admin.settings.availability');
    Route::post('admin/settings/availability', [App\Http\Controllers\AdminSettingsController::class, 'update'])->name('admin.settings.availability.update');

});
//Tableau de bord par filière
Route::get('filieres/{filiere}/stats', [FiliereStatsController::class, 'index'])->name('filieres.stats');

// === Espace Stagiaire (Portal) ===
Route::prefix('espace-stagiaire')->name('trainee.')->group(function () {
    Route::get('login', [App\Http\Controllers\TraineeAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [App\Http\Controllers\TraineeAuthController::class, 'login']);
    
    Route::middleware(['auth.trainee'])->group(function () {
        Route::get('password/setup', [App\Http\Controllers\TraineeAuthController::class, 'showPasswordSetupForm'])->name('password.setup');
        Route::post('password/setup', [App\Http\Controllers\TraineeAuthController::class, 'setupPassword'])->name('password.store');
        
        Route::get('dashboard', [App\Http\Controllers\TraineePortalController::class, 'dashboard'])->name('dashboard');
        Route::post('requests', [App\Http\Controllers\TraineePortalController::class, 'storeRequest'])->name('requests.store');
        Route::post('logout', [App\Http\Controllers\TraineeAuthController::class, 'logout'])->name('logout');
    });
});
// Routes avec rôles (admin, agent)
Route::middleware(['auth', 'role:admin|agent'])->group(function () {

    Route::get('/trainees/{trainee}/report', [TraineeController::class, 'downloadReport'])
         ->name('trainees.report');

});

// Auth routes (Laravel Breeze / Jetstream)
require __DIR__.'/auth.php';