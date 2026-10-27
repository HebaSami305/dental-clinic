<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ToothRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ToothRecordController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:chart')];}
    public function update(Request $request, Patient $patient, int $tooth)
    {
        abort_unless(in_array($tooth, ToothRecord::allTeeth(), true), 404);

        $data = $request->validate([
            'condition' => ['required', Rule::in(array_keys(ToothRecord::CONDITIONS))],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // السن السليم بدون ملاحظات ما بنخزنه
        if ($data['condition'] === 'healthy' && blank($data['notes'] ?? null)) {
            $patient->toothRecords()->where('tooth_number', $tooth)->delete();

            return response()->json(['condition' => 'healthy', 'notes' => null]);
        }

        $record = $patient->toothRecords()->updateOrCreate(
            ['tooth_number' => $tooth],
            $data
        );

        return response()->json([
            'condition' => $record->condition,
            'notes' => $record->notes,
        ]);
    }
}
