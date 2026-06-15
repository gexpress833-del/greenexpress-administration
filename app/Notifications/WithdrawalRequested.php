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
        $isLivreur = $this->withdrawal->livreur_id !== null;
        $beneficiaryName = $this->withdrawal->beneficiary()?->name ?? 'Un utilisateur';
        $amount = number_format($this->withdrawal->amount_usd, 2);

        if ($notifiable->isAdmin()) {
            $role = $isLivreur ? 'livreur' : 'agent';

            return [
                'title' => 'Nouvelle demande de retrait',
                'message' => "Le {$role} {$beneficiaryName} a demandé un retrait de \${$amount}.",
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
            'url' => $isLivreur ? route('livreur.withdrawals.index') : route('agent.withdrawals.index'),
            'icon' => 'banknote',
            'color' => 'purple',
        ];
    }
}
