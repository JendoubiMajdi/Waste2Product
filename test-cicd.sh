#!/bin/bash

# CI/CD Local Test Script
# Run this to test CI/CD components locally before pushing

echo "🚀 Testing CI/CD Components Locally..."
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test 1: PHP Syntax Check
echo "1️⃣  Checking PHP syntax..."
if find . -path ./vendor -prune -o -name "*.php" -exec php -l {} \; 2>&1 | grep -q "No syntax errors"; then
    echo -e "${GREEN}✅ PHP syntax check passed${NC}"
else
    echo -e "${RED}❌ PHP syntax errors found${NC}"
    exit 1
fi
echo ""

# Test 2: Composer Dependencies
echo "2️⃣  Installing Composer dependencies..."
if composer install --no-progress --prefer-dist --optimize-autoloader; then
    echo -e "${GREEN}✅ Composer dependencies installed${NC}"
else
    echo -e "${RED}❌ Composer install failed${NC}"
    exit 1
fi
echo ""

# Test 3: Laravel Pint (Code Style)
echo "3️⃣  Checking code style with Laravel Pint..."
if ./vendor/bin/pint --test; then
    echo -e "${GREEN}✅ Code style check passed${NC}"
else
    echo -e "${YELLOW}⚠️  Code style issues found. Run './vendor/bin/pint' to fix${NC}"
fi
echo ""

# Test 4: Environment Setup
echo "4️⃣  Setting up test environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
    echo -e "${GREEN}✅ Environment configured${NC}"
else
    echo -e "${GREEN}✅ Environment file exists${NC}"
fi
echo ""

# Test 5: Run Tests
echo "5️⃣  Running tests..."
if php artisan test; then
    echo -e "${GREEN}✅ All tests passed${NC}"
else
    echo -e "${RED}❌ Tests failed${NC}"
    exit 1
fi
echo ""

# Test 6: Build Assets
echo "6️⃣  Building frontend assets..."
if npm ci && npm run build; then
    echo -e "${GREEN}✅ Assets built successfully${NC}"
else
    echo -e "${YELLOW}⚠️  Asset build failed or not configured${NC}"
fi
echo ""

# Test 7: Security Check
echo "7️⃣  Running security audit..."
composer audit
npm audit --audit-level=moderate
echo ""

# Summary
echo "========================================="
echo -e "${GREEN}✅ Local CI/CD checks completed!${NC}"
echo "========================================="
echo ""
echo "Next steps:"
echo "1. Fix any issues found above"
echo "2. git add ."
echo "3. git commit -m 'Your commit message'"
echo "4. git push origin main (deploys to production)"
echo "   OR"
echo "   git push origin develop (deploys to staging)"
echo ""
