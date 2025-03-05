<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\PaymentProcessor;
use App\Models\Reservation;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PlanyoController extends BaseController {
  public function webHook(Request $request) {
    $data = [
      'reservation_id' => intval($request->reservation),
      'status' => intval($request->status),
      'payment_confirming_reservation' => intval($request->payment_confirming_reservation),
      'created_at' => Carbon::createFromTimestamp($request->creation_date)
    ];

    $validator = Validator::make($data, [
      'reservation_id' => 'required|integer',
      'status' => 'required|integer',
      'payment_confirming_reservation' => 'integer',
      'created_at' => 'required',
    ]);

    if ($validator->fails()) {
      // Handle validation failure (e.g., return errors)
      Log::error(json_encode($validator->errors()));
      return response()->json(['errors' => $validator->errors()], 400);
    }
    $exist = Reservation::where('reservation_id', $data['reservation_id'])->count();
    if(!$exist) {
      $reservation = Reservation::create($data);
    } else {
      $reservation = Reservation::where('reservation_id', $data['reservation_id'])->update($data);
    }

    return response()->json(['reservation' => $reservation], 201);
  }
}