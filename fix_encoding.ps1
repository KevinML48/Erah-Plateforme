# UTF-8 Encoding Fix Script
# Reads file, replaces corrupted patterns, writes back

$files = @(
    "resources/views/pages/supporter/show.blade.php",
    "resources/views/profile/partials/update-profile-information-form.blade.php"
)

$basePath = "C:\Users\kevin\OneDrive\Bureau\Programmation\app-erah"

foreach ($relPath in $files) {
    $filePath = Join-Path $basePath $relPath
    
    if (Test-Path $filePath) {
        Write-Host "Processing: $filePath"
        
        # Read as UTF-8
        $content = [System.IO.File]::ReadAllText($filePath, [System.Text.Encoding]::UTF8)
        
        # Replace corrupted patterns
        $content = $content -replace [regex]::Escape("articlÃ©"), "article"
        $content = $content -replace [regex]::Escape("vérifiÃ©"), "vérifiée"
        $content = $content -replace [regex]::Escape("vérifiÃ©e"), "vérifiée"
        $content = $content -replace [regex]::Escape("accÃ¨"), "accès"
        $content = $content -replace [regex]::Escape("rÃ©cent"), "récent"
        $content = $content -replace [regex]::Escape("utilis"), "utilisé"
        $content = $content -replace [regex]::Escape("clÃ©"), "clé"
        
        # Write back as UTF-8
        [System.IO.File]::WriteAllText($filePath, $content, [System.Text.Encoding]::UTF8)
        
        Write-Host "  ✓ Fixed"
    } else {
        Write-Host "  ✗ File not found: $filePath"
    }
}

Write-Host "Complete!"
