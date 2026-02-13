<li class="dd-item" data-id="{{ $item->id }}">
    <div class="dd-handle d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-medium item-title">{{ $item->title }}</span>
            <span class="badge bg-light text-secondary border ms-2 small">{{ $item->type }}</span>
        </div>
        <div class="dd-nodrag btn-group">
            <button type="button" class="btn btn-sm btn-secondary-soft edit-item-btn" 
                data-id="{{ $item->id }}" 
                data-title="{{ $item->title }}" 
                data-url="{{ $item->url }}" 
                data-target="{{ $item->target }}">
                <i class="fas fa-pen"></i>
            </button>
            <button type="button" class="btn btn-sm btn-danger-soft delete-item-btn" data-id="{{ $item->id }}">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
    
    @if($item->children->count() > 0)
        <ol class="dd-list">
            @foreach($item->children as $child)
                @include('admin.menus.partials.item', ['item' => $child])
            @endforeach
        </ol>
    @endif
</li>
