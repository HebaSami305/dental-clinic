<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ToothRecord;
use App\Models\Visit;
use App\Models\VisitProcedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class VisitController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:visits.manage')];}
    public function create(Patient $patient)
    {
        return view('visits.create', compact('patient'));
    }

    public function store(Request $request, Patient $patient)
    {
        [$visitData, $procedures] = $this->validated($request);

        DB::transaction(function () use ($patient, $visitData, $procedures, $request) {
            $visit = $patient->visits()->create($visitData);
            $visit->procedures()->createMany($procedures);

            if ($request->boolean('update_chart')) {
                $this->syncChart($patient, $procedures);
            }
        });

        return redirect()
            ->route('patients.show', [$patient, 'tab' => 'visits'])
            ->with('success', 'تم تسجيل الزيارة');
    }

    public function edit(Visit $visit)
    {
        $visit->load('procedures', 'patient');

        return view('visits.edit', compact('visit'));
        abort_if($visit->invoice()->exists(), 403, 'لا يمكن تعديل أو حذف زيارة لها فاتورة');
    }

    public function update(Request $request, Visit $visit)
    {
        [$visitData, $procedures] = $this->validated($request);

        DB::transaction(function () use ($visit, $visitData, $procedures, $request) {
            $visit->update($visitData);
            $visit->procedures()->delete();
            $visit->procedures()->createMany($procedures);

            if ($request->boolean('update_chart')) {
                $this->syncChart($visit->patient, $procedures);
                abort_if($visit->invoice()->exists(), 403, 'لا يمكن تعديل أو حذف زيارة لها فاتورة');
            }
        });

        return redirect()
            ->route('patients.show', [$visit->patient_id, 'tab' => 'visits'])
            ->with('success', 'تم تحديث الزيارة');
    }

    public function destroy(Visit $visit)
    {
        $patientId = $visit->patient_id;
        $visit->delete();

        return redirect()
            ->route('patients.show', [$patientId, 'tab' => 'visits'])
            ->with('success', 'تم حذف الزيارة');
            abort_if($visit->invoice()->exists(), 403, 'لا يمكن تعديل أو حذف زيارة لها فاتورة');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'visited_at' => ['required', 'date'],
            'diagnosis' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'procedures' => ['required', 'array', 'min:1'],
            'procedures.*.name' => ['required', 'string', 'max:100'],
            'procedures.*.tooth_number' => ['nullable', 'integer', Rule::in(ToothRecord::allTeeth())],
            'procedures.*.price' => ['required', 'numeric', 'min:0', 'max:100000'],
        ], [], [
            'visited_at' => 'تاريخ الزيارة',
            'diagnosis' => 'التشخيص',
            'notes' => 'الملاحظات',
            'procedures' => 'الإجراءات',
            'procedures.*.name' => 'اسم الإجراء',
            'procedures.*.tooth_number' => 'رقم السن',
            'procedures.*.price' => 'السعر',
        ]);

        $procedures = collect($data['procedures'])->map(fn ($p) => [
            'name' => $p['name'],
            'tooth_number' => $p['tooth_number'] ?? null,
            'price' => $p['price'],
        ])->values()->all();

        unset($data['procedures']);

        return [$data, $procedures];
    }

    /**
     * يحدّث مخطط الأسنان حسب نوع الإجراء (حشوة ← حشوة، علاج عصب ← علاج عصب...)
     */
    private function syncChart(Patient $patient, array $procedures): void
    {
        foreach ($procedures as $p) {
            $condition = VisitProcedure::CATALOG[$p['name']]['condition'] ?? null;

            if ($p['tooth_number'] && $condition) {
                $patient->toothRecords()->updateOrCreate(
                    ['tooth_number' => $p['tooth_number']],
                    ['condition' => $condition]
                );
            }
        }
    }
}
