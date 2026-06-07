<?php

namespace Nepal360\FilamentCmsPro\Tests\Unit;

use Nepal360\FilamentCmsPro\Tests\TestCase;
use Nepal360\FilamentCmsPro\SEO\Services\SeoEngineService;

class ReadabilityCalculationsTest extends TestCase
{
    private SeoEngineService $seoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seoService = new SeoEngineService();
    }

    public function test_flesch_reading_ease_score_matches_formula_targets(): void
    {
        // Simple sentence structure (approx 90-100 target)
        $simpleText = "The cat sat on the mat. The dog lay on the rug.";
        $simpleScore = $this->seoService->calculateFleschKincaidReadingEase($simpleText);

        $this->assertGreaterThan(90.0, $simpleScore);

        // Complex sentence structure with long syllables (approx 20-40 target)
        $complexText = "The biological diversification of ecological communities represents an evolutionary phenomenon.";
        $complexScore = $this->seoService->calculateFleschKincaidReadingEase($complexText);

        $this->assertLessThan(40.0, $complexScore);
    }

    public function test_keyword_density_calculator_percentage_targets(): void
    {
        $text = "Hiking in Nepal is beautiful. Hiking offers high peak trails.";
        
        // 2 occurrences of 'hiking' in a 10-word text (approx 20%)
        $density = $this->seoService->calculateKeywordDensity($text, 'hiking');

        $this->assertEquals(20.0, $density);
    }
}
