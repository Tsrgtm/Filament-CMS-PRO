<?php

namespace Nepal360\FilamentCmsPro\SEO\Services;

use Nepal360\FilamentCmsPro\Models\Post;

class SeoEngineService
{
    /**
     * Calculate Flesch-Kincaid Reading Ease score.
     * Formula: 206.835 - (1.015 * ASL) - (84.6 * ASW)
     */
    public function calculateFleschKincaidReadingEase(string $text): float
    {
        $text = strip_tags($text);
        
        // Count sentences (split on periods, exclamation marks, question marks)
        $sentencesCount = max(1, count(preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY)));
        
        // Count words
        $words = preg_split('/\s+/', preg_replace('/[^\w\s]/', '', $text), -1, PREG_SPLIT_NO_EMPTY);
        $wordsCount = max(1, count($words));
        
        // Count syllables
        $syllablesCount = 0;
        foreach ($words as $word) {
            $syllablesCount += $this->countWordSyllables(strtolower($word));
        }
        $syllablesCount = max(1, $syllablesCount);

        $asl = $wordsCount / $sentencesCount;
        $asw = $syllablesCount / $wordsCount;

        $score = 206.835 - (1.015 * $asl) - (84.6 * $asw);

        return round(max(0.0, min(100.0, $score)), 2);
    }

    /**
     * Count syllables in a single word (heuristic approach).
     */
    protected function countWordSyllables(string $word): int
    {
        $word = trim($word);
        if (strlen($word) <= 3) {
            return 1;
        }

        $word = preg_replace('/es$/', '', $word);
        $word = preg_replace('/e$/', '', $word);
        
        preg_match_all('/[aeiouy]{1,2}/', $word, $matches);
        
        return max(1, count($matches[0] ?? []));
    }

    /**
     * Calculate Keyword Density percentage.
     */
    public function calculateKeywordDensity(string $text, string $keyword): float
    {
        if (empty($keyword)) {
            return 0.0;
        }

        $text = strtolower(strip_tags($text));
        $keyword = strtolower($keyword);
        
        // Count word matches
        $occurrences = substr_count($text, $keyword);
        
        // Count total words
        $words = preg_split('/\s+/', preg_replace('/[^\w\s]/', '', $text), -1, PREG_SPLIT_NO_EMPTY);
        $wordsCount = max(1, count($words));

        $density = ($occurrences / $wordsCount) * 100;

        return round($density, 2);
    }

    /**
     * Generate HTML Meta Tags for Post.
     */
    public function generateMetaTags(Post $post): string
    {
        $translation = $post->translation;
        if (!$translation) {
            return '';
        }

        $metaTitle = $translation->seo_title ?: $translation->title;
        $metaDesc = $translation->seo_description ?: $translation->excerpt;
        $canonical = url('/posts/' . $translation->slug);
        $image = $post->featured_image ? url($post->featured_image) : asset('default-post.png');

        $tags = [
            "<title>" . e($metaTitle) . "</title>",
            "<meta name=\"description\" content=\"" . e($metaDesc) . "\">",
            "<link rel=\"canonical\" href=\"" . e($canonical) . "\">",
            "<meta name=\"robots\" content=\"" . ($post->sitemap_enabled ? 'index, follow' : 'noindex, nofollow') . "\">",
            
            // OpenGraph tags
            "<meta property=\"og:title\" content=\"" . e($metaTitle) . "\">",
            "<meta property=\"og:description\" content=\"" . e($metaDesc) . "\">",
            "<meta property=\"og:image\" content=\"" . e($image) . "\">",
            "<meta property=\"og:type\" content=\"article\">",
            "<meta property=\"og:url\" content=\"" . e($canonical) . "\">",
            
            // Twitter cards
            "<meta name=\"twitter:card\" content=\"summary_large_image\">",
            "<meta name=\"twitter:title\" content=\"" . e($metaTitle) . "\">",
            "<meta name=\"twitter:description\" content=\"" . e($metaDesc) . "\">",
            "<meta name=\"twitter:image\" content=\"" . e($image) . "\">",
        ];

        return implode("\n", $tags);
    }
}
