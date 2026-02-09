@extends('layouts.admin')

@section('page_title', __('Product Reviews'))

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Rating') }}</th>
                            <th>{{ __('Comment') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>
                                    @if($review->product)
                                        <a href="{{ route('shop.product.show', $review->product->slug) }}" target="_blank" class="text-decoration-none">
                                            {{ $review->product->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('common.na') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($review->user)
                                        <div>{{ $review->user->name }}</div>
                                        <small class="text-muted">{{ $review->user->email }}</small>
                                    @else
                                        <span class="text-muted">{{ __('common.na') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $review->comment }}">
                                        {{ $review->comment }}
                                    </div>
                                </td>
                                <td>
                                    @if($review->is_approved)
                                        <span class="badge bg-success">{{ __('Approved') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="is_approved" value="{{ $review->is_approved ? 0 : 1 }}">
                                            <button type="submit" class="btn btn-sm {{ $review->is_approved ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $review->is_approved ? __('Reject') : __('Approve') }}">
                                                <i class="fa-solid {{ $review->is_approved ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">{{ __('No reviews found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
@endsection
