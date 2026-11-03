<div class="features">
    @if(filled($block['title'] ?? null))
        <h2>{{ $block['title'] }}</h2>
    @endif
    <div class="features-grid">
        @foreach($block['items'] as $item)
            <div class="feature">
                {!! \App\Support\PageIcons::svg($item['icon'] ?? null) !!}
                <h4>{{ $item['title'] }}</h4>
                @if(filled($item['text'] ?? null))
                    <p>{!! nl2br(e($item['text'])) !!}</p>
                @endif
            </div>
        @endforeach
    </div>
</div>
