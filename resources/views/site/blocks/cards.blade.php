<div class="cards-container">
    @foreach($block['items'] as $item)
        <div class="card">
            <h3 class="accordion">{{ $item['title'] }}<img src="{{ asset('img/chevron.svg') }}" alt=""></h3>
            {{-- L'accordéon mobile de site.js ouvre le 2e élément après le titre. --}}
            <span hidden></span>
            <div>
                <p>{!! nl2br(e($item['text'])) !!}</p>
                @if(filled($item['url'] ?? null))
                    <a href="{{ $item['url'] }}">En savoir plus</a>
                @endif
                @if($image = \App\Support\PageBlocks::imageUrl($item['image'] ?? null))
                    <img src="{{ $image }}" alt="">
                @endif
            </div>
        </div>
    @endforeach
</div>
