<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
  protected $fillable = ['reservation_id', 'status', 'payment_confirming_reservation'];
}
