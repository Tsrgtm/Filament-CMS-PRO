<figure class="cms-block-image" style="margin: 2rem 0; text-align: center;">
    <img src="{{ asset('storage/' . $image_path) }}" 
         alt="{{ $alt ?? '' }}" 
         style="max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); transition: transform 0.3s ease;">
    @if(!empty($caption))
        <figcaption style="margin-top: 0.75rem; font-size: 0.9rem; color: #6b7280; font-style: italic;">
            {{ $caption }}
        </figcaption>
    @endif
</figure>
