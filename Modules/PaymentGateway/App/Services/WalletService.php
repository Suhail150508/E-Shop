<?php

namespace Modules\PaymentGateway\App\Services;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Modules\PaymentGateway\App\Models\WalletTransaction;

class WalletService extends BaseService
{
    public function getBalance(User $user): float
    {
        return (float) $user->wallet_balance;
    }

    public function credit(User $user, float $amount, string $description, ?string $paymentMethod = null, ?string $paymentId = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $paymentMethod, $paymentId) {
            $user->wallet_balance += $amount;
            $user->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'credit',
                'description' => $description,
                'payment_method' => $paymentMethod,
                'payment_transaction_id' => $paymentId,
                'status' => 'approved',
            ]);
        });
    }

    public function debit(User $user, float $amount, string $description): WalletTransaction
    {
        if ($user->wallet_balance < $amount) {
            throw new \Exception(__('Insufficient wallet balance.'));
        }

        return DB::transaction(function () use ($user, $amount, $description) {
            $user->wallet_balance -= $amount;
            $user->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'debit',
                'description' => $description,
                'status' => 'approved',
            ]);
        });
    }
}
