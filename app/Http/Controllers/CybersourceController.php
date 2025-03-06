<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\PaymentProcessor;
use App\Models\Reservation;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CybersourceController extends BaseController {
  public function webHook(Request $request) {
    Log::info("Cybersource Webhook" . json_encode($request->all()));
  }

  public function webHookHealthCheck(Request $request) {
    return response()->json(['message' => 'working well'], 200);
  }
}