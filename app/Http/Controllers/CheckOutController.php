<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\PaymentProcessor;

class CheckOutController extends BaseController
{
  public function checkOut(Request $request) {
    $reservation_id = $request->query('reservation_id');
    $cart_items = $request->query('cart_items');
    $items_total = $request->query('items_total');
    $items_total = str_replace('$ ','', $items_total);
    $paymentProcessor = new PaymentProcessor();
    $data = $paymentProcessor->getCartItems($cart_items);
    if (isset($data['response_code']) && $data['response_code'] === 3) {
        echo "error";
        die();
    } else {
      $data['card_id'] = $cart_items;
      $data['items_total'] = $items_total;
      $data['data'] = $data;
      return view('checkout.checkout', $data);
    }
  }

  public function checkOutP(Request $request) {
    $reservation_id = $request->query('reservation_id');
    $cart_items = $request->query('cart_items');
    $items_total = $request->query('items_total');

    $queryParams = [
        'reservation_id' => $reservation_id,
        'cart_items' => $cart_items,
        'items_total' => $items_total
    ];

    return redirect()->route('checkout', $queryParams);
  }

  public function checkOutProcess(Request $request) {
    $cart_id = $request->cart_id;
    $card_number = $request->card_number;
    $card_holder = preg_replace('/\s+/', ' ', trim($request->card_holder));
    $expiry_date = $request->expiry_date;
    $cvc = $request->cvc;
    $flag = $request->flag;
    $paymentProcessor = new PaymentProcessor();
    $firstReservation = [];
    $carts = $paymentProcessor->getCartItems($cart_id);
    if (isset($carts['response_code']) && $carts['response_code'] === 0) {
      $reservation_ids = [];
      $amounts = [];
      foreach($carts['data']['items'] as $index => $item) {
        array_push($reservation_ids, $item['reservation_id']);
        $reservation = $paymentProcessor->getReservationData($item['reservation_id']);
        array_push($amounts, $reservation['data']['total_price'] ?? 0);
        if ($index === 0) {
            $firstReservation = $reservation['data'] ?? [];
        }
      }
      $payment = $paymentProcessor->makePayment([
        "reservationIds" => $reservation_ids,
        "firstName" => trim(explode(' ', $card_holder)[0]),
        "lastName" => trim(explode(' ', $card_holder)[1]) ?? trim(explode(' ', $card_holder)[0]),
        "number" => $card_number,
        "expirationMonth" =>  explode("/", $expiry_date)[0],
        "expirationYear" => "20" + explode("/", $expiry_date)[1],
        "totalAmount" => $amounts,
        "currency" => $carts['data']['items'][0]['currency'],
        'email' => $carts['data']['items'][0]['email'],
        'country' => $carts['data']['items'][0]['country'],
        'phoneNumber' => $carts['data']['items'][0]['mobile_number'],
        "flag" => $flag,
      ]);
      $payment['reservationData'] = $firstReservation;
      $payment['reservationId'] = $reservation_ids[0];
      return $payment;
    } else {
      return [
        "statusCode" => 400,
        "message" => 'Something went wrong!'
      ];
    }  
  }
}
