<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Metadata Injection -->
    {!! Nepal360\FilamentCmsPro\Support\CmsFacade::renderSeo($post, $locale) !!}

    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; color: #1f2937; margin: 0; padding: 2rem; background-color: #f9fafb; }
        .cms-container { max-width: 48rem; margin: 0 auto; background: white; padding: 3rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .cms-block-heading { color: #111827; margin-top: 2rem; margin-bottom: 1rem; }
        .cms-block-paragraph { line-height: 1.7; color: #374151; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <div class="cms-container">
        <article class="cms-post-container">
            <header class="cms-post-header">
                <h1 class="cms-post-title" style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem;">
                    {{ $post->translation->title }}
                </h1>
                <div class="cms-post-meta" style="color: #6b7280; font-size: 0.875rem; margin-bottom: 2rem;">
                    <span>Published on: {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Draft' }}</span>
                    <span> &bull; {{ $post->read_time_minutes }} min read</span>
                </div>
            </header>

            <div class="cms-post-content">
                {!! Nepal360\FilamentCmsPro\Support\CmsFacade::renderBlocks($post->translation->content) !!}
            </div>
        </article>
    </div>
</body>
</html>
