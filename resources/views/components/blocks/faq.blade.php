<div class="cms-block-faq" style="margin: 2.5rem 0; display: flex; flex-direction: column; gap: 1rem;">
    @foreach($items as $item)
        <details style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; cursor: pointer; transition: box-shadow 0.2s ease;">
            <summary style="font-weight: 600; color: #111827; outline: none; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                <span>{{ $item['question'] }}</span>
                <span class="faq-arrow" style="font-size: 0.8rem; transition: transform 0.2s ease;">▼</span>
            </summary>
            <p style="margin-top: 0.75rem; color: #4b5563; line-height: 1.6; font-size: 0.95rem;">
                {{ $item['answer'] }}
            </p>
        </details>
    @endforeach
</div>
