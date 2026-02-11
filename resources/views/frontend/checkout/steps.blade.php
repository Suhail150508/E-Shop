

<div class="checkout-steps">
    <div class="stepper-wrapper">
        <!-- Step 1: Add to Cart -->
        <div class="stepper-item {{ $currentStep > 1 ? 'completed' : ($currentStep == 1 ? 'active' : '') }}">
            <div class="step-counter">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="step-name">{{ __('common.step_add_to_cart') }}</div>
        </div>

        <!-- Step 2: Check & Proceed -->
        <div class="stepper-item {{ $currentStep > 2 ? 'completed' : ($currentStep == 2 ? 'active' : '') }}">
            <div class="step-counter">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="step-name">{{ __('common.step_check_proceed') }}</div>
        </div>

        <!-- Step 3: Payment -->
        <div class="stepper-item {{ $currentStep > 3 ? 'completed' : ($currentStep == 3 ? 'active' : '') }}">
            <div class="step-counter">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="step-name">{{ __('common.step_payment') }}</div>
        </div>

        <!-- Step 4: Confirmation -->
        <div class="stepper-item {{ $currentStep > 4 ? 'completed' : ($currentStep == 4 ? 'active' : '') }}">
            <div class="step-counter">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="step-name">{{ __('common.step_confirmation') }}</div>
        </div>
    </div>
</div>
