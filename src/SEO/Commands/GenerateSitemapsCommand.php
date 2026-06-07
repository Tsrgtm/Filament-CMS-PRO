<?php

namespace Nepal360\FilamentCmsPro\SEO\Commands;

use Illuminate\Console\Command;
use Nepal360\FilamentCmsPro\SEO\Services\SitemapGeneratorService;

class GenerateSitemapsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cms:generate-sitemaps';

    /**
     * The console command description.
     */
    protected $description = 'Regenerate the standard posts and Google News XML sitemaps';

    /**
     * Execute the console command.
     */
    public function handle(SitemapGeneratorService $generator): int
    {
        $this->info('Starting sitemap regeneration...');
        
        try {
            $generator->generateAll();
            $this->info('Sitemaps regenerated successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to generate sitemaps: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
