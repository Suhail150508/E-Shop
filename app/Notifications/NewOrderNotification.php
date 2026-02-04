<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->order->total,
            'customer_name' => $this->order->customer_name,
            'message' => 'New order #'.$this->order->order_number.' placed by '.$this->order->customer_name,
            'link' => route('admin.orders.show', $this->order->id),
            'type' => 'new_order',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->order->total,
            'customer_name' => $this->order->customer_name,
            'message' => 'New order #'.$this->order->order_number.' placed by '.$this->order->customer_name,
            'updated_at' => now()->toIso8601String(),
            'link' => route('admin.orders.show', $this->order->id),
        ]);
    }
}
