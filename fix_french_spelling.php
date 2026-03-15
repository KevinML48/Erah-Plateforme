<?php

/**
 * Corrects French spelling errors across the entire codebase
 * Run: php fix_french_spelling.php
 */

$replacements = [
    // Common apostrophe errors
    ' c est ' => " C'est ",
    ' c est.' => " C'est.",
    ' d abord' => " d'abord",
    ' d avoir' => " d'avoir",
    ' d entree' => " d'entrée",
    ' d un' => " d'un",
    ' d une' => " d'une",
    ' j ai' => " j'ai",
    ' l idee' => " l'idée",
    ' l activite' => " l'activité",
    ' l engagement' => " l'engagement",
    ' l espace' => " l'espace",
    ' l heure' => " l'heure",
    ' l interet' => " l'intérêt",
    ' m ont' => " m'ont",
    ' n est' => " n'est",
    ' qu une' => " qu'une",
    ' s appliquent' => " s'appliquent",
    ' s ajouter' => " s'ajouter",
    " t aider" => " t'aider",
    " l avancee" => " l'avancée",
    " l etat" => " l'état",
    " l interet" => " l'intérêt",
    
    // Missing accents
    'reponse' => 'réponse',
    'reponses' => 'réponses',
    'recuperer' => 'récupérer',
    'recupererez' => 'récupérerez',
    'determiner' => 'déterminer',
    'determine' => 'déterminé',
    'determines' => 'déterminés',
    'detaille' => 'détaillé',
    'reglement' => 'règlement',
    'reglage' => 'réglage',
    'reglages' => 'réglages',
    'repere' => 'repère',
    'reperes' => 'repères',
    'delibere' => 'délibéré',
    'denonce' => 'dénoncé',
    'departement' => 'département',
    'depot' => 'dépôt',
    'debit' => 'débit',
    'desactif' => 'désactivé',
    'desactive' => 'désactivée',
    'desastre' => 'désastre',
    'desormais' => 'désormais',
    'detail' => 'détail',
    'details' => 'détails',
    'deverrouille' => 'déverrouillé',
    'developpement' => 'développement',
    'decouvrir' => 'découvrir',
    'decouverte' => 'découverte',
    'demarrer' => 'démarrer',
    'demande' => 'demande',
    'demandes' => 'demandes',
    'denomination' => 'dénomination',
    
    'preference' => 'préférence',
    'preferences' => 'préférences',
    'premiere' => 'première',
    'premières' => 'premières',
    'precedent' => 'précédent',
    'precedente' => 'précédente',
    'precise' => 'précise',
    'preciser' => 'préciser',
    'predire' => 'prédire',
    'preuve' => 'preuve',
    'prevenir' => 'prévenir',
    'preventivement' => 'préventivement',
    'prevu' => 'prévu',
    'prevue' => 'prévue',
    'prevus' => 'prévus',
    'prevues' => 'prévues',
    'privilege' => 'privilège',
    'privilegier' => 'privilégier',
    'process' => 'processus',
    
    'gerer' => 'gérer',
    'geree' => 'gérée',
    'gerees' => 'gérées',
    'general' => 'général',
    'generale' => 'générale',
    'generales' => 'générales',
    'generee' => 'générée',
    'generees' => 'générées',
    'genetique' => 'génétique',
    'generer' => 'générer',
    
    'creer' => 'créer',
    'creee' => 'créée',
    'creees' => 'créées',
    'createur' => 'créateur',
    'creation' => 'création',
    'creations' => 'créations',
    'complete' => 'complète',
    'completement' => 'complètement',
    'competence' => 'compétence',
    'competences' => 'compétences',
    'competition' => 'compétition',
    'competitif' => 'compétitif',
    'complementaire' => 'complémentaire',
    'complement' => 'complément',
    'complet' => 'complet',
    
    'reference' => 'référence',
    'referencer' => 'référencer',
    'referent' => 'référent',
    'referentiel' => 'référentiel',
    'refuser' => 'refuser',
    'refusee' => 'refusée',
    'resultat' => 'résultat',
    'resultats' => 'résultats',
    'residence' => 'résidence',
    'resident' => 'résident',
    
    'coeur' => 'cœur',
    'heure' => 'heure',
    'heures' => 'heures',
    'entree' => 'entrée',
    'entrees' => 'entrées',
    
    // Specific phrase fixes
    'du centre d aide' => "du centre d'aide",
    'du centre d aide' => "du centre d'aide",
    'Au moins d une' => "Au moins d'une",
    'A l abri' => "À l'abri",
    'A l aide' => "À l'aide",
    'A l appui' => "À l'appui",
    'A l assaut' => "À l'assaut",
    'A l avance' => "À l'avance",
];

// Get all PHP and Blade files
function getPhpFiles($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && (strpos($file->getPathname(), '.php') !== false)) {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

$files = array_merge(
    getPhpFiles('app'),
    getPhpFiles('config'),
    getPhpFiles('database'),
    getPhpFiles('resources'),
);

$totalChanges = 0;
$changedFiles = [];

foreach ($files as $file) {
    if (strpos($file, 'fix_french_spelling.php') !== false) {
        continue;
    }
    
    $content = file_get_contents($file);
    $originalContent = $content;
    
    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        $changes = count(array_keys(explode($search, $originalContent))) - 1;
        $totalChanges += $changes;
        $changedFiles[] = $file;
        echo "✓ Fixed: $file\n";
    }
}

echo "\n✅ Total changes: $totalChanges\n";
echo "✅ Files updated: " . count($changedFiles) . "\n";
echo "\nDone! You can now commit these changes.\n";
