<div class="testimonials">
    @if(filled($block['title'] ?? null))
        <h2>{{ $block['title'] }}</h2>
    @endif
    <div class="pin-container stacked">
        @foreach($block['items'] as $item)
            <div class="pin">
                @if($image = \App\Support\PageBlocks::imageUrl($item['image'] ?? null))
                    <img src="{{ $image }}" class="face" alt="">
                @endif
                <p>“{!! nl2br(e($item['quote'])) !!}”</p>
                <span class="name">{{ $item['name'] }}@if(filled($item['role'] ?? null))<br>{{ $item['role'] }}@endif</span>
            </div>
        @endforeach
    </div>
</div>
