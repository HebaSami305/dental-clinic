<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware; 

class InvoiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:billing')];
    }
    public function index(Request $request)
    {
        $paidSql = '(select coalesce(sum(amount), 0) from payments where payments.invoice_id = invoices.id)';

        $invoices = Invoice::with('patient')
            ->withSum('payments', 'amount')
            ->when($request->q, function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('number', 'like', "%{$term}%")
                      ->orWhereHas('patient', fn ($p) => $p->withTrashed()->where('name', 'like', "%{$term}%"));
                });
            })
            ->when($request->status === 'unpaid', fn ($q) => $q->whereRaw("$paidSql = 0 and total > 0"))
            ->when($request->status === 'partial', fn ($q) => $q->whereRaw("$paidSql > 0 and $paidSql < total"))
            ->when($request->status === 'paid', fn ($q) => $q->whereRaw("$paidSql >= total"))
            ->latest('issued_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $billed = (float) Invoice::sum('total');
        $collected = (float) Payment::sum('amount');

        return view('invoices.index', [
            'invoices' => $invoices,
            'billed' => $billed,
            'collected' => $collected,
            'outstanding' => $billed - $collected,
        ]);
    }

    public function store(Visit $visit)
    {
        if ($visit->invoice) {
            return redirect()->route('invoices.show', $visit->invoice);
        }

        $visit->load('procedures');
        $subtotal = (float) $visit->procedures->sum('price');

        $invoice = DB::transaction(function () use ($visit, $subtotal) {
            $invoice = Invoice::create([
                'patient_id' => $visit->patient_id,
                'visit_id' => $visit->id,
                'issued_at' => now(),
                'subtotal' => $subtotal,
                'discount' => 0,
                'total' => $subtotal,
            ]);

            $invoice->items()->createMany(
                $visit->procedures->map(fn ($p) => [
                    'tooth_number' => $p->tooth_number,
                    'description' => $p->name,
                    'price' => $p->price,
                ])->all()
            );

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'تم إنشاء الفاتورة');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('patient', 'items', 'payments');

        return view('invoices.show', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'discount' => ['required', 'numeric', 'min:0', 'max:' . $invoice->subtotal],
            'notes' => ['nullable', 'string'],
        ], [], [
            'discount' => 'الخصم',
            'notes' => 'الملاحظات',
        ]);

        $total = round((float) $invoice->subtotal - (float) $data['discount'], 2);

        if ($total < $invoice->paid) {
            return back()->withErrors(['discount' => 'هالخصم بيخلي إجمالي الفاتورة أقل من المبلغ المدفوع']);
        }

        $invoice->update([
            'discount' => $data['discount'],
            'notes' => $data['notes'] ?? null,
            'total' => $total,
        ]);

        return back()->with('success', 'تم تحديث الفاتورة');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->payments()->exists()) {
            return back()->withErrors(['invoice' => 'لا يمكن حذف فاتورة عليها دفعات. احذف الدفعات أولاً']);
        }

        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'تم حذف الفاتورة');
    }
}
