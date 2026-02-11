

<div class="checkout-steps mb-5">
    <div class="stepper-wrapper">
        <!-- Step 1: Add to Cart -->
        <div class="stepper-item {{ $currentStep > 1 ? 'completed' : ($currentStep == 1 ? 'active' : '') }}">
            <div class="step-counter">
                @if($currentStep > 1)
                    <i class="fas fa-check"></i>
                @else
                    <i class="fas fa-shopping-cart"></i>
                @endif
            </div>
            <div class="step-name">{{ __('common.step_add_to_cart') }}</div>
        </div>

        <!-- Step 2: Check & Proceed -->
        <div class="stepper-item {{ $currentStep > 2 ? 'completed' : ($currentStep == 2 ? 'active' : '') }}">
            <div class="step-counter">
                @if($currentStep > 2)
                    <i class="fas fa-check"></i>
                @else
                    <i class="fas fa-clipboard-check"></i>
                @endif
            </div>
            <div class="step-name">{{ __('common.step_check_proceed') }}</div>
        </div>

        <!-- Step 3: Payment -->
        <div class="stepper-item {{ $currentStep > 3 ? 'completed' : ($currentStep == 3 ? 'active' : '') }}">
            <div class="step-counter">
                @if($currentStep > 3)
                    <i class="fas fa-check"></i>
                @else
                    <i class="fas fa-hand-holding-usd"></i>
                @endif
            </div>
            <div class="step-name">{{ __('common.step_payment') }}</div>
        </div>

        <!-- Step 4: Confirmation -->
        <div class="stepper-item {{ $currentStep > 4 ? 'completed' : ($currentStep == 4 ? 'active' : '') }}">
            <div class="step-counter">
                @if($currentStep > 4)
                    <i class="fas fa-check"></i>
                @else
                    <i class="fas fa-check-circle"></i>
                @endif
            </div>
            <div class="step-name">{{ __('common.step_confirmation') }}</div>
        </div>
    </div>
</div>
