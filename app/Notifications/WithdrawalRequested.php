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
        $user = $this->withdrawal->user ?? $this->withdrawal->agent;
        $userName = $user?->name ?? 'Utilisateur';
        $amount = number_format($this->withdrawal->amount_usd, 2);

        if ($notifiable->isAdmin()) {
            return [
                'title' => 'Nouvelle demande de retrait',
                'message' => "{$userName} a demandé un retrait de \${$amount}.",
                'withdrawal_id' => $this->withdrawal->id,
                'url' => route('admin.withdrawals.index'),
                'icon' => 'banknote',
                'color' => 'purple',
            ];
        }

        return [
            'title' => 'Demande de retrait envoyée',
            'message' => "Votre demande de retrait de \${$amount} a été envoyée et est en attente de validation.",
            'withdrawal_id' => $this->withdrawal->id,
            'url' => $user?->isLivreur() ? route('livreur.withdrawals.index') : route('agent.withdrawals.index'),
            'icon' => 'banknote',
            'color' => 'purple',
        ];
    }
}
