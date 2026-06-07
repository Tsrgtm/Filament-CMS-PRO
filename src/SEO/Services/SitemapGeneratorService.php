<?php

namespace Nepal360\FilamentCmsPro\SEO\Services;

use Nepal360\FilamentCmsPro\Models\Post;
use Illuminate\Support\Facades\Storage;

class SitemapGeneratorService
{
    /**
     * Generate sitemaps files in public folder.
     */
    public function generateAll(): void
    {
        $this->generatePostsSitemap();
        $this->generateNewsSitemap();
    }

    /**
     * Generate standard posts sitemap.
     */
    protected function generatePostsSitemap(): void
    {
        $posts = Post::where('status', 'published')
            ->where('sitemap_enabled', true)
            ->whereNotNull('published_at')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($posts as $post) {
            foreach ($post->translations as $trans) {
                $xml .= "  <url>\n";
                $xml .= "    <loc>" . e(url('/posts/' . $trans->slug)) . "</loc>\n";
                $xml .= "    <lastmod>" . $post->updated_at->toIso8601String() . "</lastmod>\n";
                $xml .= "    <changefreq>daily</changefreq>\n";
                $xml .= "    <priority>0.8</priority>\n";
                $xml .= "  </url>\n";
            }
        }

        $xml .= '</urlset>';

        file_put_contents(public_path('sitemap-posts.xml'), $xml);
    }

    /**
     * Generate sitemap news specifically for last 48 hours.
     */
    protected function generateNewsSitemap(): void
    {
        $posts = Post::where('status', 'published')
            ->where('sitemap_enabled', true)
            ->where('published_at', '>=', now()->subHours(48))
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

        foreach ($posts as $post) {
            foreach ($post->translations as $trans) {
                $xml .= "  <url>\n";
                $xml .= "    <loc>" . e(url('/posts/' . $trans->slug)) . "</loc>\n";
                $xml .= "    <news:news>\n";
                $xml .= "      <news:publication>\n";
                $xml .= "        <news:name>" . e(config('app.name', 'Filament CMS Pro')) . "</news:name>\n";
                $xml .= "        <news:language>" . e($trans->locale) . "</news:language>\n";
                $xml .= "      </news:publication>\n";
                $xml .= "      <news:publication_date>" . $post->published_at->toIso8601String() . "</news:publication_date>\n";
                $xml .= "      <news:title>" . e($trans->title) . "</news:title>\n";
                $xml .= "    </news:news>\n";
                $xml .= "  </url>\n";
            }
        }

        $xml .= '</urlset>';

        file_put_contents(public_path('sitemap-news.xml'), $xml);
    }
}
