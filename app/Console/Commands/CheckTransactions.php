<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckTransactions extends Command
{
    protected $signature = 'check:transactions';
    protected $description = 'Check for transactions created between 5 and 10 minutes ago';

    public function handle()
    {
      Log::info("check:transactions is running");
        $tenMinutesAgo = Carbon::now()->subMinutes(10);
        $fiveMinutesAgo = Carbon::now()->subMinutes(1);

        $transactions = Reservation::whereBetween('created_at', [$tenMinutesAgo, $fiveMinutesAgo])->get();

        foreach ($transactions as $transaction) {
          Log::info(json_encode(json_encode($transaction)));
            // Process each transaction here
            $this->info("Processing transaction ID: " . $transaction->id);
        }
    }
}
