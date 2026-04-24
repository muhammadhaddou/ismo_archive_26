<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$trainees = App\Models\Trainee::all();

$md = "# Liste des Accès Stagiaires (Portail ISMO Archive)\n\n";
$md .= "Ce tableau répertorie les 150 stagiaires enregistrés, avec leurs informations de connexion.\n";
$md .= "* **Email / CEF** : Identifiant utilisé pour la connexion.\n";
$md .= "* **Mot de passe (1ère connexion)** : La CIN du stagiaire sert de mot de passe temporaire s'il ne l'a pas encore changé.\n\n";
$md .= "| Nom & Prénom | Email / CEF | Date de naissance | Mot de passe initial (CIN) | Mot de passe configuré ? |\n";
$md .= "|---|---|---|---|---|\n";

foreach($trainees as $t) {
    $configured = !empty($t->password) ? '✅ Oui' : '❌ Non (Utiliser CIN)';
    $md .= "| " . $t->last_name . " " . $t->first_name . " | `" . $t->cef . "` | `" . $t->date_naissance . "` | `" . $t->cin . "` | " . $configured . " |\n";
}

file_put_contents('c:\Users\pc\Desktop\ismo_archive_v20\Liste_Acces_Stagiaires.md', $md);
echo "Terminé.";
