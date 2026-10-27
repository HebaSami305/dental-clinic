<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PaymentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:billing')];
    }
    public function store(Request $request, Invoice $invoice)
    {
        $balance = max($invoice->balance, 0);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $balance],
            'method' => ['required', Rule::in(array_keys(Payment::METHODS))],
            'paid_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'amount.max' => 'المبلغ أكبر من الرصيد المتبقي (' . number_format($balance, 2) . ' ₪)',
        ], [
            'amount' => 'المبلغ',
            'method' => 'طريقة الدفع',
            'paid_at' => 'تاريخ الدفع',
            'notes' => 'الملاحظات',
        ]);

        $invoice->payments()->create($data);

        return back()->with('success', 'تم تسجيل الدفعة');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return back()->with('success', 'تم حذف الدفعة');
    }
}
