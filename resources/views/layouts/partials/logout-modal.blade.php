@php
    $tLogout = trans('common.logout');
    $tClose = trans('common.close');
    $tCancel = trans('common.cancel');
    $tConfirm = trans('common.logout_confirmation');
    if ($tLogout === 'common.logout') $tLogout = 'Logout';
    if ($tClose === 'common.close') $tClose = 'Close';
    if ($tCancel === 'common.cancel') $tCancel = 'Cancel';
    if ($tConfirm === 'common.logout_confirmation') $tConfirm = 'Are you sure you want to logout?';
    $tHint = trans('common.you_can_sign_in_again');
    if ($tHint === 'common.you_can_sign_in_again') $tHint = 'You can sign in again anytime.';
@endphp
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4 pb-0">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3 text-danger">
                        <i class="fas fa-sign-out-alt fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="modal-title fw-bold mb-0" id="logoutModalLabel">{{ $tLogout }}</h4>
                        <p class="text-muted small mb-0">{{ $tHint }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close align-self-start" data-bs-dismiss="modal" aria-label="{{ $tClose }}"></button>
            </div>
            <div class="modal-body p-4 pt-3">
                <p class="fs-5 text-dark mb-0">{{ $tConfirm }}</p>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light px-4 py-2 fw-medium" data-bs-dismiss="modal">{{ $tCancel }}</button>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger px-4 py-2 fw-bold shadow-sm">{{ $tLogout }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
