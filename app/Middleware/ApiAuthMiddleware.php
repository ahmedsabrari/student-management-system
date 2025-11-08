<?php

declare(strict_types=1); // Enforce strict types

namespace App\Middleware;

use App\Helpers\Response;
use App\Core\Env; // Assuming Env helper exists

/**
 * API Authentication Middleware
 *
 * Verifies an API token provided in the 'Authorization' header
 * against a predefined list or database of valid tokens.
 * Rejects requests with missing or invalid tokens using a 401 Unauthorized response.
 * This should be applied to all protected API routes, usually after CORS middleware.
 */
class ApiAuthMiddleware
{
    /**
     * Handle the incoming request.
     * معالجة الطلب الوارد.
     *
     * Extracts the token from the 'Authorization: Bearer <token>' header
     * and validates it.
     * استخراج الرمز من هيدر 'Authorization: Bearer <token>' والتحقق منه.
     *
     * @return void
     */
    public static function handle(): void
    {
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        $token = null;

        // 1. Check if the Authorization header exists and is in 'Bearer <token>' format.
        // ١. التحقق من وجود هيدر Authorization ومن أنه بصيغة 'Bearer <token>'.
        if ($authorizationHeader && preg_match('/^Bearer\s+(.+)$/', $authorizationHeader, $matches)) {
            $token = $matches[1];
        }

        // 2. If no token was found, reject the request.
        // ٢. إذا لم يتم العثور على رمز، ارفض الطلب.
        if (!$token) {
            Response::error('Authentication token missing or invalid.', 401); // 401 Unauthorized
            // Response::error() handles exit() automatically.
            return;
        }

        // 3. Validate the extracted token.
        // ٣. التحقق من صحة الرمز المستخرج.
        if (!self::isValidToken($token)) {
            // Token is invalid
            // الرمز غير صالح
            Response::error('Invalid authentication token.', 401); // 401 Unauthorized
            // Response::error() handles exit()
            return;
        }

        // If the token is valid, do nothing and let the request proceed.
        // إذا كان الرمز صالحًا، لا تفعل شيئًا ودع الطلب يستمر.
    }

    /**
     * Validates a given API token.
     * التحقق من صحة رمز API معين.
     *
     * Placeholder implementation: Checks against a simple list.
     * In a real application, this should check against a database table
     * (e.g., 'api_tokens') or validate a JWT signature.
     * تنفيذ مؤقت: يتحقق مقابل قائمة بسيطة.
     * في تطبيق حقيقي، يجب التحقق مقابل جدول قاعدة بيانات أو التحقق من توقيع JWT.
     *
     * @param string $token The token to validate.
     * @return bool True if the token is valid, false otherwise.
     */
    private static function isValidToken(string $token): bool
    {
        // Example: Get allowed tokens from environment variable (comma-separated)
        // مثال: الحصول على الرموز المسموح بها من متغير البيئة (مفصولة بفواصل)
        $validTokensEnv = Env::get('VALID_API_TOKENS', ''); // e.g., "token123,anothertoken456"
        $validTokens = array_filter(array_map('trim', explode(',', $validTokensEnv)));

        // VERY basic check - is the provided token in our list?
        // تحقق أساسي جدًا - هل الرمز المقدم موجود في قائمتنا؟
        if (!empty($validTokens)) {
            return in_array($token, $validTokens, true); // Use strict comparison
        }
        
        // --- Alternative: Database Check (Conceptual) ---
        // $apiTokenModel = new \App\Models\ApiToken(); // Assuming model exists
        // $tokenRecord = $apiTokenModel->findByToken($token);
        // if ($tokenRecord && !$tokenRecord->isExpired() && $tokenRecord->is_active) {
        //     // Optionally, associate the request with the user linked to the token
        //     // Session::set('api_user_id', $tokenRecord->user_id); 
        //     return true;
        // }

        // --- Alternative: JWT Validation (Conceptual) ---
        // try {
        //     $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key(Env::get('JWT_SECRET'), 'HS256'));
        //     // Optionally check claims (e.g., expiration, issuer)
        //     // Session::set('api_user_id', $decoded->sub); // Set user ID from token subject
        //     return true;
        // } catch (\Exception $e) {
        //     // Token invalid (signature failed, expired, etc.)
        //     return false;
        // }

        return false; // Default to invalid if no valid method found
    }
}