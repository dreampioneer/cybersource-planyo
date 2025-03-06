<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PaymentProcessor;

class CheckTransactions extends Command
{
    protected $signature = 'check:transactions';
    protected $description = 'Check for transactions created between 5 and 10 minutes ago';

    public function handle()
    {
        Log::info("check:transactions is running");
        $tenMinutesAgo = Carbon::now()->subMinutes(3600);
        $fiveMinutesAgo = Carbon::now()->subMinutes(1);

        $carts = Reservation::whereBetween('creation_time', [$tenMinutesAgo, $fiveMinutesAgo])->where('status', 2)->where('captured', 0)->orderBy('creation_time', 'asc')->get()->groupBy('cart_id');
        Log::info(json_encode($carts));
        $paymentProcessor = new PaymentProcessor();
        foreach ($carts as $key => $cart) {
            $amounts = [];
            $reservation_ids = [];
            foreach ($cart as $key2 => $cart2) {
                $reservation = $paymentProcessor->getReservationData($cart2->reservation_id);
                array_push($reservation_ids, $cart2->reservation_id);
                array_push($amounts, $reservation['data']['total_price'] ?? 0);
            }
            $result = $paymentProcessor->capturePayment(['id' => $cart2->transaction_id, 'code' => $reservation_ids[0],'amount'=> array_sum($amounts)]);
            Reservation::whereIn('reservation_id', $reservation_ids)->update(['captured'=> 1]);
            if ($result['status'] == 'success') {
                $paymentProcessor->addReservationPayment([
                    'reservation_ids'=> $reservation_ids,
                    'payment_mode' => 40,
                    'payment_status' => $result['paymentStatus'],
                    'transaction_id' => $result['transaction_id'],
                    'amount' => $amounts,
                    'currency' => 'SGD',
                    'method' => 'add_reservation_payment',
                    'language' => 'EN',
                ]);
            } else {

            }
        }
    }
}
