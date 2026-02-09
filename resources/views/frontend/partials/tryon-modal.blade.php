{{-- Virtual Try-On Modal: available on all pages so customers can try on from product cards or detail --}}
<div class="modal fade tryon-modal" id="tryOnModal" tabindex="-1" aria-labelledby="tryOnModalLabel" aria-hidden="true"
     data-virtual-try-url="{{ route('virtual.try') }}"
     data-msg-please-upload="{{ __('common.tryon_please_upload') }}"
     data-msg-error="{{ __('common.error_generic') }}"
     data-msg-preview="{{ __('common.preview') }}"
     data-msg-processing="{{ __('common.processing') }}">
    <div class="modal-dialog modal-dialog-centered tryon-modal-dialog">
        <div class="modal-content tryon-modal-content border-0 shadow-lg overflow-hidden">
            <div class="modal-header tryon-modal-header border-0">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="tryOnModalLabel">
                    <span class="tryon-modal-icon"><i class="fas fa-user-check" aria-hidden="true"></i></span>
                    {{ __('common.virtual_try_on') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('common.close') }}"></button>
            </div>
            <div class="modal-body tryon-modal-body">
                <div id="tryonUploadStep" class="tryon-step">
                    <div class="tryon-upload-header d-flex align-items-center justify-content-between gap-2 mb-3">
                        <span class="tryon-upload-label">{{ __('common.upload_your_photo') }}</span>
                        <span class="tryon-tips-trigger" id="tryonPhotoTipsTrigger" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-html="false" title="{{ __('common.tryon_photo_tips') }}" aria-label="{{ __('common.tryon_photo_tips') }}">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                        </span>
                    </div>
                    <div class="tryon-dropzone" id="tryonDropzone">
                        <input type="file" id="customerImage" accept="image/jpeg,image/jpg,image/png" class="tryon-file-input" aria-label="{{ __('common.upload_your_photo') }}">
                        <div class="tryon-dropzone-inner">
                            <i class="fas fa-cloud-upload-alt tryon-dropzone-icon" aria-hidden="true"></i>
                            <p class="tryon-dropzone-text mb-1">{{ __('common.tryon_click_or_drag') }}</p>
                            <p class="tryon-dropzone-hint small text-muted mb-0">{{ __('common.tryon_best_results') }}</p>
                        </div>
                        <div class="tryon-selected-preview d-none" id="tryonSelectedPreview">
                            <div class="tryon-selected-preview-bar">
                                <button type="button" class="tryon-selected-remove" id="tryonRemoveSelection" aria-label="{{ __('common.remove') }}"><i class="fas fa-times" aria-hidden="true"></i></button>
                            </div>
                            <div class="tryon-selected-img-wrap">
                                <img id="tryonSelectedImg" src="" alt="" class="tryon-selected-img">
                            </div>
                        </div>
                    </div>
                    <button type="button" id="startTryBtn" class="btn btn-primary tryon-btn-preview w-100 mt-4 position-relative" disabled>
                        <span class="btn-text">{{ __('common.preview') }}</span>
                        <span class="loader-inline d-none" role="status" aria-hidden="true"><span class="block"></span><span class="block"></span><span class="block"></span><span class="block"></span></span>
                    </button>
                </div>
                <div id="tryonResultStep" class="tryon-step d-none tryon-result-step-layout">
                    <div class="tryon-result-preview-area">
                        <div class="tryon-result-wrap position-relative">
                            <img id="previewImage" class="tryon-result-img img-fluid" src="" alt="{{ __('common.preview') }}">
                            <div id="tryonResultAdjust" class="tryon-result-adjust d-none position-relative mx-auto overflow-hidden rounded" style="max-width: 100%; max-height: 100%;">
                                <div id="tryonResultBg" class="tryon-result-bg position-absolute top-0 start-0 w-100 h-100 bg-dark" style="background-size: contain; background-repeat: no-repeat; background-position: center;"></div>
                                <img id="tryonResultOverlay" class="tryon-result-overlay position-absolute" src="" alt="" style="pointer-events: none;" role="presentation">
                            </div>
                        </div>
                    </div>
                    <div class="tryon-result-toolbar">
                        <div id="tryonPositionControls" class="tryon-position-controls d-none mt-3">
                            <div class="tryon-controls-panel rounded-3 p-3 p-md-4">
                                <h6 class="tryon-controls-title text-center mb-3">{{ __('common.tryon_adjust_overlay') }}</h6>
                                <div class="tryon-control-group mb-3">
                                    <p class="tryon-control-label small mb-2 text-center">{{ __('common.tryon_adjust_position') }}</p>
                                    <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                                        <button type="button" class="btn tryon-move-btn" id="tryonMoveUp" aria-label="{{ __('common.tryon_move_up') }}" title="{{ __('common.tryon_move_up') }}"><i class="fas fa-chevron-up" aria-hidden="true"></i></button>
                                        <button type="button" class="btn tryon-move-btn" id="tryonMoveDown" aria-label="{{ __('common.tryon_move_down') }}" title="{{ __('common.tryon_move_down') }}"><i class="fas fa-chevron-down" aria-hidden="true"></i></button>
                                        <button type="button" class="btn tryon-move-btn" id="tryonMoveLeft" aria-label="{{ __('common.tryon_move_left') }}" title="{{ __('common.tryon_move_left') }}"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
                                        <button type="button" class="btn tryon-move-btn" id="tryonMoveRight" aria-label="{{ __('common.tryon_move_right') }}" title="{{ __('common.tryon_move_right') }}"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                                <div class="tryon-control-group">
                                    <p class="tryon-control-label small mb-2 text-center">{{ __('common.tryon_width') }}</p>
                                    <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                                        <button type="button" class="btn tryon-size-btn" id="tryonWidthLess" aria-label="{{ __('common.tryon_decrease_width') }}" title="{{ __('common.tryon_decrease_width') }}">
                                            <i class="fas fa-minus me-1" aria-hidden="true"></i><span class="tryon-size-label">{{ __('common.tryon_decrease_width') }}</span>
                                        </button>
                                        <button type="button" class="btn tryon-size-btn" id="tryonWidthMore" aria-label="{{ __('common.tryon_increase_width') }}" title="{{ __('common.tryon_increase_width') }}">
                                            <i class="fas fa-plus me-1" aria-hidden="true"></i><span class="tryon-size-label">{{ __('common.tryon_increase_width') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tryon-result-actions d-flex flex-wrap gap-2 justify-content-center mt-4">
                            <button type="button" class="btn btn-success btn-lg rounded-pill px-4" id="downloadBtn">
                                <i class="fas fa-download me-2"></i>{{ __('common.download') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-lg rounded-pill px-4" id="retryBtn">
                                <i class="fas fa-redo me-2"></i>{{ __('common.try_again') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('frontend/js/tryon.js') }}" defer></script>
@endpush
