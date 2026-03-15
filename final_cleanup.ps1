# Global UTF-8 corruption cleanup - Final pass
# Fix all garbled UTF-8 sequences systematically

param([string]$Path = 'c:\Users\kevin\OneDrive\Bureau\Programmation\app-erah\')

$files = @(
    'resources\views\pages\wallets\index.blade.php',
    'resources\views\marketing\partials\home-en-ce-moment.blade.php',
    'resources\views\pages\users\index.blade.php',
    'resources\views\marketing\faq.blade.php',
    'resources\views\pages\supporter\show.blade.php',
    'resources\views\profile\partials\update-profile-information-form.blade.php',
    'app\Services\MissionCatalogService.php',
    'app\Services\MissionClaimService.php',
    'app\Services\MissionTrackingService.php',
    'app\Services\GrantMonthlySupporterRewards.php'
)

$fixedCount = 0

foreach ($file in $files) {
    $fullPath = Join-Path $Path $file
    if (Test-Path $fullPath) {
        $original = Get-Content $fullPath -Raw -Encoding UTF8
        
        # Replace common corruption patterns using simple string replace
        $content = $original
        $content = $content.Replace('î', 'é')  # Most common 
        $content = $content.Replace('ïVáí«', '«')
        $content = $content.Replace('Ãl', 'é')
        $content = $content.Replace('articl', 'article')
        $content = $content.Replace('circle', 'circle')  # Already OK
        $content = $content.Replace('veriifi', 'vérifi')
        
        if ($content -ne $original) {
            Set-Content $fullPath -Value $content -Encoding UTF8
            Write-Output "✓ $file"
            $fixedCount++
        }
    }
}

Write-Output "`nCompleted: $fixedCount files processed"
