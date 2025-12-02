#!/usr/bin/env bash

# Script de test des optimisations PERF-001
# Usage: ./test_performance.sh

echo "🧪 Test des optimisations de performance PERF-001"
echo "=================================================="
echo ""

API_URL="http://localhost:8000/api/articles"

# Test 1: Première requête (cache vide)
echo "📊 Test 1: Première requête (cache vide)"
echo "Expected: ~100-150ms"
START=$(date +%s%3N)
curl -s "$API_URL" > /dev/null
END=$(date +%s%3N)
TIME1=$((END - START))
echo "✅ Temps: ${TIME1}ms"
echo ""

# Test 2: Deuxième requête (cache hit)
echo "📊 Test 2: Deuxième requête (cache actif)"
echo "Expected: ~1-5ms"
sleep 0.5
START=$(date +%s%3N)
curl -s "$API_URL" > /dev/null
END=$(date +%s%3N)
TIME2=$((END - START))
echo "✅ Temps: ${TIME2}ms"
echo ""

# Test 3: Avec mode performance test
echo "📊 Test 3: Mode performance test (avec délai 30ms/article)"
echo "Expected: ~1500ms (50 articles × 30ms)"
START=$(date +%s%3N)
curl -s "$API_URL?performance_test=1" > /dev/null
END=$(date +%s%3N)
TIME3=$((END - START))
echo "✅ Temps: ${TIME3}ms"
echo ""

# Résultats
echo "📈 Résumé des résultats"
echo "======================"
echo "Test 1 (cache vide):    ${TIME1}ms"
echo "Test 2 (cache hit):     ${TIME2}ms"
echo "Test 3 (mode test):     ${TIME3}ms"
echo ""

# Calcul du gain
if [ $TIME1 -gt 0 ]; then
    GAIN=$((TIME1 / TIME2))
    echo "🚀 Gain avec cache: ${GAIN}x plus rapide"
fi

echo ""
echo "📋 Pour voir les requêtes SQL:"
echo "docker logs blog_backend -f"
