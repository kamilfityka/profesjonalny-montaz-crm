<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VerifyWpWebhookToken
{
    public function handle(Request $request, Closure $next)
    {
        $expected = config('services.wp_webhook.token');

        if (empty($expected)) {
            throw new HttpException(503, 'WP webhook token is not configured.');
        }

        $provided = $request->header('X-WP-Webhook-Token');

        if (!is_string($provided) || !hash_equals((string) $expected, $provided)) {
            throw new HttpException(401, 'Invalid webhook token.');
        }

        return $next($request);
    }
}
