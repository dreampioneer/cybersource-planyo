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
        
    } else {
      $data['items_total'] = $items_total;
      $data['data'] = $data;
      return view('checkout/checkout', $data);
    }
  }
}
