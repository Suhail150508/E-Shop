

<div class="breadcrumb-section" style="background-image: url('{{ $bgImage ?? 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80' }}');">
    <div class="breadcrumb-overlay"></div>
    <div class="container breadcrumb-content">
        <h1 class="text-white display-4 breadcrumb-title">{{ $title ?? '' }}</h1>
        <nav aria-label="breadcrumb" class="d-flex justify-content-center">
            <ol class="breadcrumb breadcrumb-nav mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                @if(isset($parents) && is_array($parents))
                    @foreach($parents as $parent)
                         <li class="breadcrumb-item"><a href="{{ $parent['url'] }}">{{ $parent['label'] }}</a></li>
                    @endforeach
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $title ?? '' }}</li>
            </ol>
        </nav>
    </div>
</div>
