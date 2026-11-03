<div class="support">
    @if(filled($block['title'] ?? null))
        <h2>{{ $block['title'] }}</h2>
    @endif
    <div class="slot-container stacked">
        @foreach($block['items'] as $item)
            <div class="slot">
                @if($image = \App\Support\PageBlocks::imageUrl($item['image'] ?? null))
                    <img src="{{ $image }}" alt="">
                @elseif($icon = \App\Support\PageIcons::svg($item['icon'] ?? null, 'slot-icon'))
                    {!! $icon !!}
                @endif
                @if(filled($item['title'] ?? null))
                    <h4>{{ $item['title'] }}</h4>
                @endif
                <p>{!! nl2br(e($item['text'])) !!}</p>
            </div>
        @endforeach
    </div>
</div>
