<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id', 'tooth_number', 'description', 'price'];

    protected $casts = ['price' => 'decimal:2'];
}
