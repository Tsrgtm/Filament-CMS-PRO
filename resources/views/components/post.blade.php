<article class="cms-post-container">
    <header class="cms-post-header">
        <h1 class="cms-post-title">{{ $post->translation->title }}</h1>
        <div class="cms-post-meta text-sm text-gray-500">
            <span>Published at: {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Draft' }}</span>
            <span> &bull; {{ $post->read_time_minutes }} min read</span>
        </div>
    </header>

    @if($post->featured_image)
        <div class="cms-post-featured-image my-6">
            <img src="{{ url($post->featured_image) }}" alt="{{ $post->translation->title }}" class="w-full rounded-lg">
        </div>
    @endif

    <div class="cms-post-content mt-6">
        <x-filament-cms-pro-blocks :blocks="$post->translation->content" />
    </div>
</article>
