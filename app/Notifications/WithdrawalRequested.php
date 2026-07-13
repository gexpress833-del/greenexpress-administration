<?php

namespace App\Notifications;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WithdrawalRequested extends Notification
{
    use Queueable;

    public function __construct(public Withdrawal $withdrawal) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $agentName = $this->withdrawal->agent->name;
        $amount = number_format($this->withdrawal->amount_usd, 2);

        if ($notifiable->isAdmin()) {
            return [
                'title' => 'Nouvelle demande de retrait',
                'message' => "{$agentName} a demandé un retrait de \${$amount}.",
                'withdrawal_id' => $this->withdrawal->id,
                'url' => route('admin.withdrawals.show', $this->withdrawal),
                'icon' => 'banknote',
                'color' => 'purple',
            ];
        }

        return [
            'title' => 'Demande de retrait envoyée',
            'message' => "Votre demande de retrait de \${$amount} a été envoyée et est en attente de validation.",
            'withdrawal_id' => $this->withdrawal->id,
            'url' => route('agent.withdrawals.index'),
            'icon' => 'banknote',
            'color' => 'purple',
        ];
    }
}
