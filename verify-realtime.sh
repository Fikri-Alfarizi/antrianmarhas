#!/bin/bash
# REALTIME BROADCASTING IMPLEMENTATION - INSTALLATION SCRIPT
# Run this to verify all files are in place

echo "================================"
echo "Realtime Broadcasting Verification"
echo "================================"
echo ""

# Check Documentation Files
echo "📄 Checking Documentation Files..."
docs=(
    "REALTIME_QUICKSTART.md"
    "REALTIME_SUMMARY.md"
    "REALTIME_IMPLEMENTATION_GUIDE.md"
    "REALTIME_IMPLEMENTATION_COMPLETE.md"
    "REALTIME_DOCUMENTATION_INDEX.md"
    "CHANGELOG_REALTIME.md"
)

for doc in "${docs[@]}"; do
    if [ -f "$doc" ]; then
        echo "  ✅ $doc"
    else
        echo "  ❌ $doc (MISSING)"
    fi
done

echo ""
echo "🔧 Checking Configuration Files..."

# Check modified config files
files=(
    ".env"
    "config/broadcasting.php"
    "resources/js/bootstrap.js"
    "resources/views/display/index.blade.php"
    "routes/web.php"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✅ $file"
    else
        echo "  ❌ $file (MISSING)"
    fi
done

echo ""
echo "📦 Checking New Files..."

# Check new files
new_files=(
    "app/Services/BroadcastTestService.php"
    "resources/views/test/broadcast.blade.php"
    "resources/views/display/index_realtime.blade.php"
)

for file in "${new_files[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✅ $file"
    else
        echo "  ❌ $file (MISSING)"
    fi
done

echo ""
echo "📚 Checking Node Packages..."
if grep -q "laravel-echo" package.json; then
    echo "  ✅ laravel-echo installed"
else
    echo "  ❌ laravel-echo not found in package.json"
fi

if grep -q "pusher-js" package.json; then
    echo "  ✅ pusher-js installed"
else
    echo "  ❌ pusher-js not found in package.json"
fi

echo ""
echo "✅ Verification Complete!"
echo ""
echo "Next Steps:"
echo "1. npm run build"
echo "2. php artisan config:clear"
echo "3. Open http://localhost:8000/test/broadcast"
echo "4. Read REALTIME_QUICKSTART.md for testing"
echo ""
