<div class="text-block">
    <div class="text-block-content">
        @if(filled($block['title'] ?? null))
            <h2>{{ $block['title'] }}</h2>
        @endif
        <p>{!! nl2br(e($block['text'])) !!}</p>
    </div>
    @if($image = \App\Support\PageBlocks::imageUrl($block['image'] ?? null))
        <img src="{{ $image }}" alt="">
    @endif
</div>
