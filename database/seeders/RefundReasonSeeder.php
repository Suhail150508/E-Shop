<?php

namespace Database\Seeders;

use App\Models\RefundReason;
use Illuminate\Database\Seeder;

class RefundReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            'Damaged or defective product',
            'Wrong item received',
            'Item not as described',
            'Changed my mind',
            'Delivery delay',
            'Duplicate order',
            'Other',
        ];

        foreach ($reasons as $reason) {
            RefundReason::firstOrCreate(
                ['reason' => $reason],
                ['status' => true]
            );
        }
    }
}
