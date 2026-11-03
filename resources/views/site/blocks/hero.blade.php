<div class="hero">
    <h1>{{ $block['title'] }}</h1>
    @if(filled($block['text'] ?? null))
        <p>{!! nl2br(e($block['text'])) !!}</p>
    @endif
    @if(filled($block['button_label'] ?? null))
        <a href="{{ $block['button_url'] }}" class="btn green">{{ $block['button_label'] }}</a>
    @endif
</div>
