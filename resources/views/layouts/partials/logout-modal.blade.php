@php
    $tLogout = trans('common.logout');
    $tClose = trans('common.close');
    $tCancel = trans('common.cancel');
    $tConfirm = trans('common.logout_confirmation');
    if ($tLogout === 'common.logout') $tLogout = 'Logout';
    if ($tClose === 'common.close') $tClose = 'Close';
    if ($tCancel === 'common.cancel') $tCancel = 'Cancel';
    if ($tConfirm === 'common.logout_confirmation') $tConfirm = 'Are you sure you want to logout?';
@endphp
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-2 text-primary">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <h5 class="modal-title fw-bold" id="logoutModalLabel">{{ $tLogout }}</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ $tClose }}"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-2">{{ $tConfirm }}</p>
                @php
                    $tHint = trans('common.you_can_sign_in_again');
                    if ($tHint === 'common.you_can_sign_in_again') $tHint = 'You can sign in again anytime.';
                @endphp
                <p class="text-muted small mb-0">{{ $tHint }}</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ $tCancel }}</button>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">{{ $tLogout }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
