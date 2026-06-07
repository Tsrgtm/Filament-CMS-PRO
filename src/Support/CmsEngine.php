<?php

namespace Nepal360\FilamentCmsPro\Support;

use Nepal360\FilamentCmsPro\Models\Post;
use Illuminate\Support\Facades\Cache;
use Nepal360\FilamentCmsPro\SEO\Services\SeoEngineService;

class CmsEngine
{
    /**
     * Render the entire post using caching tags.
     */
    public function renderPost(Post $post, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $cacheKey = "cms_post_{$post->id}_{$locale}_rendered";

        return Cache::remember($cacheKey, config('filament-cms-pro.cache.ttl', 86400), function () use ($post, $locale) {
            $template = $post->layout_template ?: 'standard';
            $viewName = "filament-cms-pro::templates.{$template}";

            if (view()->exists("vendor.filament-cms-pro.templates.{$template}")) {
                $viewName = "vendor.filament-cms-pro.templates.{$template}";
            }

            return view($viewName, [
                'post' => $post,
                'locale' => $locale,
            ])->render();
        });
    }

    /**
     * Compile block arrays to HTML.
     */
    public function renderBlocks(array $blocks): string
    {
        return collect($blocks)->map(function ($block) {
            $type = $block['type'] ?? 'paragraph';
            $data = $block['data'] ?? [];

            $viewName = "filament-cms-pro::components.blocks.{$type}";

            if (view()->exists("vendor.filament-cms-pro.components.blocks.{$type}")) {
                $viewName = "vendor.filament-cms-pro.components.blocks.{$type}";
            }

            if (!view()->exists($viewName)) {
                return '';
            }

            return view($viewName, $data)->render();
        })->implode("\n");
    }

    /**
     * Compile page SEO metadata tags.
     */
    public function renderSeo(Post $post, ?string $locale = null): string
    {
        return (new SeoEngineService())->generateMetaTags($post);
    }
}
