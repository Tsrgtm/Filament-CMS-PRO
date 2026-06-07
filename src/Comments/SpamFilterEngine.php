<?php

namespace Nepal360\FilamentCmsPro\Comments;

class SpamFilterEngine
{
    /**
     * Analyze content for potential spam markers.
     */
    public function analyze(string $content, array $metadata = []): bool
    {
        $content = strtolower($content);

        // Spam keywords checklist
        $blacklist = [
            'casino',
            'viagra',
            'buy links',
            'free crypto',
            'seo services',
            'cheap pharmaceuticals',
            'earn money online',
        ];

        foreach ($blacklist as $spamWord) {
            if (str_contains($content, $spamWord)) {
                return true;
            }
        }

        // Limit links inside comments to prevent link-injection spam
        if (substr_count($content, 'http://') + substr_count($content, 'https://') > 2) {
            return true;
        }

        return false;
    }
}
