<?php

namespace Nepal360\FilamentCmsPro\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Nepal360\FilamentCmsPro\Jobs\LogAnalyticsEvent;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitorAnalytics
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Perform logging post-response to avoid blocking request completion
        if ($this->shouldTrack($request, $response)) {
            $userAgent = $request->userAgent() ?? '';
            
            // Simple bot parser
            $isRobot = (bool) preg_match('/(googlebot|bingbot|yandexbot|slurp|duckduckbot|baiduspider)/i', $userAgent);
            
            // Cookieless GDPR-compliant unique hash
            $visitorHash = hash('sha256', config('app.key') . $request->ip() . $userAgent);

            // Fetch matched post route parameters
            $postId = null;
            $route = $request->route();
            if ($route) {
                $post = $route->parameter('post');
                if ($post instanceof \Nepal360\FilamentCmsPro\Models\Post) {
                    $postId = $post->id;
                } elseif (is_numeric($post)) {
                    $postId = (int) $post;
                }
            }

            LogAnalyticsEvent::dispatch([
                'post_id' => $postId,
                'path' => $request->decodedPath(),
                'referrer_host' => parse_url($request->headers->get('referer'), PHP_URL_HOST),
                'visitor_hash' => $visitorHash,
                'is_robot' => $isRobot,
                'utm_source' => $request->query('utm_source'),
                'utm_medium' => $request->query('utm_medium'),
                'utm_campaign' => $request->query('utm_campaign'),
                'viewed_at' => now()->toDateTimeString(),
            ])->onQueue('analytics');
        }

        return $response;
    }

    /**
     * Determine if the request should be tracked.
     */
    protected function shouldTrack(Request $request, Response $response): bool
    {
        if (!config('filament-cms-pro.analytics.enabled', true)) {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        // Avoid tracking internal APIs or direct JSON headless calls if excluded
        if ($request->expectsJson()) {
            return false;
        }

        // Filter out static asset calls
        $path = $request->decodedPath();
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|otf|map)$/i', $path)) {
            return false;
        }

        // Check configured exclusions
        $exclusions = config('filament-cms-pro.analytics.exclude_paths', []);
        foreach ($exclusions as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        return true;
    }
}
