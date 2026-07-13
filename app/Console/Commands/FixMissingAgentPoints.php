<?php

namespace App\Console\Commands;

use App\Events\OrderValidatedByClient;
use App\Models\Order;
use Illuminate\Console\Command;

class FixMissingAgentPoints extends Command
{
    protected $signature = 'fix:missing-agent-points';

    protected $description = 'Recrédite les points manquants pour les commandes livrées et validées';

    public function handle(): int
    {
        $orders = Order::where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->whereDoesntHave('agentPoints')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Aucune commande manquante trouvée.');

            return 0;
        }

        $count = 0;
        foreach ($orders as $order) {
            event(new OrderValidatedByClient($order));
            $count++;
            $this->info("Points crédités pour la commande {$order->code} (agent: {$order->agent->name})");
        }

        $this->newLine();
        $this->info("{$count} commande(s) traitée(s).");

        return 0;
    }
}
