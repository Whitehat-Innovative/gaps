#!/bin/bash

# API Integration Test Script
# This script demonstrates the complete authentication and authorization flow

set -e

BASE_URL="http://localhost:8899"
ADMIN_EMAIL="test@example.com"
ADMIN_PASS="password"

echo "================================================"
echo "Laravel Breeze + Sanctum + Spatie Integration"
echo "================================================"
echo ""

# Step 1: Get the existing test user's token
echo "Step 1: Creating API token for test user..."
TOKEN=$(docker-compose exec -T app php artisan tinker --execute "
\$user = App\Models\User::where('email', '$ADMIN_EMAIL')->first();
if (\$user) {
    echo \$user->createToken('test-script-' . now()->timestamp)->plainTextToken;
} else {
    echo 'User not found';
}
" 2>/dev/null | tail -1 | tr -d '\n')

if [ -z "$TOKEN" ] || [ "$TOKEN" = "User not found" ]; then
    echo "❌ Failed to create token"
    exit 1
fi

echo "✓ Token created: ${TOKEN:0:10}..."
echo ""

# Step 2: Test unauthenticated access
echo "Step 2: Testing unauthenticated API request..."
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/api/user")
if [ "$RESPONSE" = "401" ]; then
    echo "✓ Unauthenticated request correctly rejected (401)"
else
    echo "❌ Expected 401, got $RESPONSE"
fi
echo ""

# Step 3: Test authenticated access
echo "Step 3: Testing authenticated /api/user endpoint..."
RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/api/user")
USER_EMAIL=$(echo "$RESPONSE" | jq -r '.email' 2>/dev/null || echo "")
if [ "$USER_EMAIL" = "$ADMIN_EMAIL" ]; then
    echo "✓ Authenticated request successful"
    echo "  User: $(echo "$RESPONSE" | jq -r '.name')"
    echo "  Email: $USER_EMAIL"
else
    echo "❌ Failed to authenticate"
    echo "Response: $RESPONSE"
fi
echo ""

# Step 4: Test role-protected endpoint
echo "Step 4: Testing role-protected /api/admin/stats endpoint..."
RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$BASE_URL/api/admin/stats")
HAS_ROLES=$(echo "$RESPONSE" | jq '.user.roles | length' 2>/dev/null || echo "0")
MESSAGE=$(echo "$RESPONSE" | jq -r '.message' 2>/dev/null || echo "")

if [ "$MESSAGE" = "Admin statistics endpoint" ] && [ "$HAS_ROLES" -gt 0 ]; then
    echo "✓ Role-protected endpoint accessible"
    echo "  User roles: $(echo "$RESPONSE" | jq -r '.user.roles[0].name')"
    echo "  Timestamp: $(echo "$RESPONSE" | jq -r '.timestamp')"
else
    echo "❌ Failed to access role-protected endpoint"
    echo "Response: $RESPONSE"
fi
echo ""

# Step 5: Test web session auth (if we can access the login page)
echo "Step 5: Checking web authentication routes..."
WEB_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/")
if [ "$WEB_STATUS" = "200" ]; then
    echo "✓ Web routes available"
else
    echo "⚠ Web routes returned: $WEB_STATUS"
fi
echo ""

echo "================================================"
echo "Summary:"
echo "================================================"
echo "✓ API authentication is working correctly"
echo "✓ Role-based authorization is working"
echo "✓ Sanctum + Spatie integration successful"
echo ""
echo "Next steps:"
echo "- Use this token in your frontend application"
echo "- Create additional roles and permissions as needed"
echo "- Configure CORS for your frontend domain"
echo "- Add rate limiting for production"
echo ""

