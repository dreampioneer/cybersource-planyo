<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\PaymentProcessor;
use App\Models\Reservation;
use Illuminate\Support\Facades\Validator;

class PlanyoController extends BaseController {
  public function webHook(Request $request) {
    $data = [
      'reservation_id' => $request->reservation,
      'status' => $request->status,
      'payment_confirming_reservation' => $request->payment_confirming_reservation,
    ];

    $validator = Validator::make($data, [
      'reservation_id' => 'required|integer',
      'status' => 'required|integer',
      'payment_confirming_reservation' => 'integer',
    ]);

    if ($validator->fails()) {
      // Handle validation failure (e.g., return errors)
      return response()->json(['errors' => $validator->errors()], 400);
    }

    // If validation passes, create the new reservation
    $reservation = Reservation::create($data);

    return response()->json(['reservation' => $reservation], 201);
  }
}