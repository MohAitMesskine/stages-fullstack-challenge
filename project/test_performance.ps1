# Script de test des optimisations PERF-001
# Usage: .\test_performance.ps1

Write-Host "🧪 Test des optimisations de performance PERF-001" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

$apiUrl = "http://localhost:8000/api/articles"

# Test 1: Première requête (cache vide)
Write-Host "📊 Test 1: Première requête (cache vide)" -ForegroundColor Yellow
Write-Host "Expected: ~100-150ms" -ForegroundColor Gray

$time1 = Measure-Command {
    Invoke-WebRequest -Uri $apiUrl -UseBasicParsing -ErrorAction SilentlyContinue | Out-Null
}
Write-Host "✅ Temps: $([math]::Round($time1.TotalMilliseconds, 0))ms" -ForegroundColor Green
Write-Host ""

# Test 2: Deuxième requête (cache hit)
Write-Host "📊 Test 2: Deuxième requête (cache actif)" -ForegroundColor Yellow
Write-Host "Expected: ~1-5ms" -ForegroundColor Gray

Start-Sleep -Milliseconds 500
$time2 = Measure-Command {
    Invoke-WebRequest -Uri $apiUrl -UseBasicParsing -ErrorAction SilentlyContinue | Out-Null
}
Write-Host "✅ Temps: $([math]::Round($time2.TotalMilliseconds, 0))ms" -ForegroundColor Green
Write-Host ""

# Test 3: Avec mode performance test
Write-Host "📊 Test 3: Mode performance test (avec délai 30ms/article)" -ForegroundColor Yellow
Write-Host "Expected: ~1500ms (50 articles × 30ms)" -ForegroundColor Gray

$time3 = Measure-Command {
    Invoke-WebRequest -Uri "$apiUrl?performance_test=1" -UseBasicParsing -ErrorAction SilentlyContinue | Out-Null
}
Write-Host "✅ Temps: $([math]::Round($time3.TotalMilliseconds, 0))ms" -ForegroundColor Green
Write-Host ""

# Résultats
Write-Host "📈 Résumé des résultats" -ForegroundColor Cyan
Write-Host "======================" -ForegroundColor Cyan
Write-Host "Test 1 (cache vide):    $([math]::Round($time1.TotalMilliseconds, 0))ms"
Write-Host "Test 2 (cache hit):     $([math]::Round($time2.TotalMilliseconds, 0))ms"
Write-Host "Test 3 (mode test):     $([math]::Round($time3.TotalMilliseconds, 0))ms"
Write-Host ""

# Calcul du gain
if ($time2.TotalMilliseconds -gt 0) {
    $gain = [math]::Round($time1.TotalMilliseconds / $time2.TotalMilliseconds, 1)
    Write-Host "🚀 Gain avec cache: ${gain}x plus rapide" -ForegroundColor Green
}

Write-Host ""
Write-Host "📋 Pour voir les requêtes SQL:" -ForegroundColor Yellow
Write-Host "docker logs blog_backend -f" -ForegroundColor White
Write-Host ""
Write-Host "🌐 Pour tester dans le navigateur:" -ForegroundColor Yellow
Write-Host "http://localhost:3000 (puis cliquer sur '🧪 Tester Performance')" -ForegroundColor White
