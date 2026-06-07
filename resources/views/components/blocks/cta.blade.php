<div class="cms-block-cta" style="margin: 2.5rem 0; text-align: center; padding: 2rem; border-radius: 16px; background: rgba(99, 102, 241, 0.04); border: 1px solid rgba(99, 102, 241, 0.08);">
    @if($style === 'primary')
        <a href="{{ $url }}" 
           target="_blank" 
           style="display: inline-block; padding: 0.85rem 2rem; font-weight: 600; text-decoration: none; border-radius: 30px; color: #ffffff; background: linear-bezier(135deg, #6366f1, #4f46e5); box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4); transition: transform 0.2s ease, box-shadow 0.2s ease;">
            {{ $text }}
        </a>
    @else
        <a href="{{ $url }}" 
           target="_blank" 
           style="display: inline-block; padding: 0.85rem 2rem; font-weight: 600; text-decoration: none; border-radius: 30px; color: #4f46e5; background: #ffffff; border: 1px solid #4f46e5; transition: transform 0.2s ease, background 0.2s ease;">
            {{ $text }}
        </a>
    @endif
</div>
