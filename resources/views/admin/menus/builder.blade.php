@extends('layouts.admin')

@section('page_title', 'Menu Builder')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.css">
<style>
    .dd { max-width: 100%; }
    .dd-handle { height: auto; padding: 10px 15px; border: 1px solid #e2e8f0; background: #fff; border-radius: 6px; margin-bottom: 10px; font-weight: 500; cursor: move; transition: all 0.2s; }
    .dd-handle:hover { background: #f8fafc; border-color: #cbd5e1; }
    .dd-item > button { margin-top: 8px; }
    .dd-placeholder { border: 1px dashed #cbd5e1; background: #f1f5f9; border-radius: 6px; margin-bottom: 10px; min-height: 40px; }
    .dd-list .dd-list { padding-left: 30px; }
    .dd-dragel > .dd-item > .dd-handle { border-left: 5px solid #4f46e5; }
</style>
@endpush

@section('content')
<div class="row">
    <!-- Left Sidebar: Add Items -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h6 class="card-title mb-0 fw-bold">{{ __('Add Menu Items') }}</h6>
            </div>
            <div class="card-body p-0">
                <div class="accordion accordion-flush" id="addItemAccordion">
                    <!-- Custom Link -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCustom">
                                {{ __('Custom Link') }}
                            </button>
                        </h2>
                        <div id="collapseCustom" class="accordion-collapse collapse show" data-bs-parent="#addItemAccordion">
                            <div class="accordion-body">
                                <div class="mb-2">
                                    <label class="form-label small">{{ __('Parent Item') }}</label>
                                    <select class="form-select form-select-sm" id="custom-parent">
                                        <option value="">{{ __('No Parent (Root)') }}</option>
                                        @foreach($allMenuItems as $menuItem)
                                            <option value="{{ $menuItem->id }}">{{ $menuItem->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">{{ __('URL') }}</label>
                                    <input type="text" class="form-control form-control-sm" id="custom-url" placeholder="https://">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">{{ __('Link Text') }}</label>
                                    <input type="text" class="form-control form-control-sm" id="custom-title" placeholder="Menu Item">
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary w-100" id="add-custom-btn">
                                    {{ __('Add to Menu') }}
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Right Content: Menu Structure -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold">{{ __('Menu Structure') }}</h6>
                <button type="button" class="btn btn-sm btn-primary" id="save-menu-btn">
                    <i class="fas fa-save me-1"></i> {{ __('Save Menu') }}
                </button>
            </div>
            <div class="card-body">
                <!-- Menu Settings -->
                <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" class="mb-4 bg-light p-3 rounded">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Menu Name') }}</label>
                            <input type="text" class="form-control" name="name" value="{{ $menu->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Display Location') }}</label>
                            <select class="form-select" name="position">
                                <option value="">{{ __('Select Position') }}</option>
                                <option value="header" {{ $menu->position == 'header' ? 'selected' : '' }}>{{ __('Header Main Menu') }}</option>
                                <option value="footer_1" {{ $menu->position == 'footer_1' ? 'selected' : '' }}>{{ __('Footer Column 1') }}</option>
                                <option value="footer_2" {{ $menu->position == 'footer_2' ? 'selected' : '' }}>{{ __('Footer Column 2') }}</option>
                                <option value="footer_3" {{ $menu->position == 'footer_3' ? 'selected' : '' }}>{{ __('Footer Column 3') }}</option>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-sm btn-dark">{{ __('Update Settings') }}</button>
                        </div>
                    </div>
                </form>

                <hr class="my-4">

                <div class="dd" id="menu-nestable">
                    <ol class="dd-list">
                        @foreach($menuItems as $item)
                            @include('admin.menus.partials.item', ['item' => $item])
                        @endforeach
                    </ol>
                    @if($menuItems->isEmpty())
                        <div class="text-center text-muted py-5" id="empty-menu-msg">
                            <i class="fas fa-arrows-alt fa-2x mb-3 opacity-50"></i>
                            <p>{{ __('Add items from the left column to build your menu.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Item') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-id">
                <div class="mb-3">
                    <label class="form-label">{{ __('Navigation Label') }}</label>
                    <input type="text" class="form-control" id="edit-title">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('URL') }}</label>
                    <input type="text" class="form-control" id="edit-url">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Target') }}</label>
                    <select class="form-select" id="edit-target">
                        <option value="_self">Same Tab</option>
                        <option value="_blank">New Tab</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="update-item-btn">{{ __('Save Changes') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js"></script>
<script>
    $(document).ready(function() {
        const menuId = {{ $menu->id }};
        const csrfToken = '{{ csrf_token() }}';

        // Initialize Nestable
        $('#menu-nestable').nestable({
            maxDepth: 3
        });

        // Add Custom Link
        $('#add-custom-btn').click(function() {
            const title = $('#custom-title').val();
            const url = $('#custom-url').val();
            const parentId = $('#custom-parent').val();
            
            if(!title) {
                toastr.error('Link text is required');
                return;
            }

            addItem({
                title: title,
                url: url || '#',
                type: 'custom',
                target: '_self',
                parent_id: parentId
            });
            
            $('#custom-title').val('');
            $('#custom-url').val('');
            $('#custom-parent').val('');
        });

        // AJAX Add Item
        function addItem(data) {
            $.ajax({
                url: "{{ route('admin.menus.item.add', $menu->id) }}",
                type: 'POST',
                data: {
                    _token: csrfToken,
                    ...data
                },
                success: function(res) {
                    if(res.success) {
                        $('#empty-menu-msg').remove();
                        
                        if (res.item.parent_id) {
                            const parentLi = $('.dd-item[data-id="' + res.item.parent_id + '"]');
                            if (parentLi.length) {
                                let list = parentLi.children('ol.dd-list');
                                if (!list.length) {
                                    list = $('<ol class="dd-list"></ol>');
                                    parentLi.append(list);
                                }
                                list.append(res.html);
                            } else {
                                // Fallback if parent not found in DOM
                                $('.dd-list').first().append(res.html);
                            }
                        } else {
                            $('.dd-list').first().append(res.html);
                        }
                        
                        // Add to dropdown
                        $('#custom-parent').append(new Option(res.item.title, res.item.id));
                        
                        toastr.success('Item added');
                    }
                },
                error: function() {
                    toastr.error('Failed to add item');
                }
            });
        }

        // Delete Item
        $(document).on('click', '.delete-item-btn', function() {
            if(!confirm('Delete this item?')) return;
            
            const btn = $(this);
            const id = btn.data('id');

            $.ajax({
                url: "{{ url('admin/menus/items') }}/" + id,
                type: 'DELETE',
                data: { _token: csrfToken },
                success: function(res) {
                    if(res.success) {
                        btn.closest('.dd-item').remove();
                        toastr.success('Item deleted');
                    }
                }
            });
        });

        // Edit Item Modal
        $(document).on('click', '.edit-item-btn', function() {
            const btn = $(this);
            $('#edit-id').val(btn.data('id'));
            $('#edit-title').val(btn.data('title'));
            $('#edit-url').val(btn.data('url'));
            $('#edit-target').val(btn.data('target'));
            $('#editItemModal').modal('show');
        });

        // Update Item
        $('#update-item-btn').click(function() {
            const id = $('#edit-id').val();
            const data = {
                title: $('#edit-title').val(),
                url: $('#edit-url').val(),
                target: $('#edit-target').val(),
                _token: csrfToken
            };

            $.ajax({
                url: "{{ url('admin/menus/items') }}/" + id,
                type: 'PUT',
                data: data,
                success: function(res) {
                    if(res.success) {
                        const item = $(`.dd-item[data-id="${id}"]`);
                        item.find('.item-title').first().text(data.title);
                        const btn = item.find('.edit-item-btn');
                        btn.data('title', data.title);
                        btn.data('url', data.url);
                        btn.data('target', data.target);
                        
                        $('#editItemModal').modal('hide');
                        toastr.success('Item updated');
                    }
                }
            });
        });

        // Save Menu Order
        $('#save-menu-btn').click(function() {
            const order = $('#menu-nestable').nestable('serialize');
            
            $.ajax({
                url: "{{ route('admin.menus.sort') }}",
                type: 'POST',
                data: {
                    _token: csrfToken,
                    order: JSON.stringify(order)
                },
                success: function(res) {
                    toastr.success('Menu structure saved');
                },
                error: function() {
                    toastr.error('Failed to save menu structure');
                }
            });
        });
    });
</script>
@endpush
