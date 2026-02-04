<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public $status;

    public function __construct(Order $order, $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->status,
            'message' => 'Your order #'.$this->order->order_number.' status updated to '.$this->status,
            'link' => route('customer.orders.show', $this->order->id),
            'type' => 'order_status_updated',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'order_id' => $this->order->id,
            'status' => $this->status,
            'message' => 'Your order #'.$this->order->order_number.' status updated to '.$this->status,
            'updated_at' => now()->toIso8601String(),
            'link' => route('customer.orders.show', $this->order->id),
            'type' => 'order_status_updated',
        ]);
    }
}
