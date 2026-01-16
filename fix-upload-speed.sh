#!/bin/bash

echo "=========================================="
echo "Fix Upload Speed Issues"
echo "=========================================="
echo ""

echo "Step 1: Clearing all caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
echo "✅ Caches cleared"
echo ""

echo "Step 2: Checking queue table..."
php artisan queue:table 2>/dev/null
php artisan migrate --force
echo "✅ Queue table ready"
echo ""

echo "Step 3: Running diagnostic..."
php test-upload-speed.php
echo ""

echo "=========================================="
echo "Configuration Applied:"
echo "=========================================="
echo "- Compression threshold: 10MB (only very large files)"
echo "- Compression quality: 75% (faster)"
echo "- Max dimensions: 1200x1200px (faster)"
echo "- Queue conversions: DISABLED (faster)"
echo ""
echo "This should make uploads much faster!"
echo ""
echo "Test by uploading a large image now."
echo "=========================================="
