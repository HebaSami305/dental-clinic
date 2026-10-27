<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware; 

class PatientController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:patients.view', only: ['index', 'show']),
            new Middleware('can:patients.manage', except: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $patients = Patient::query()
            ->search($request->q)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }
    public function show(Patient $patient)
{
    return view('patients.show', compact('patient'));
    $patient->load('visits.procedures', 'toothRecords');
    return view('patients.show', compact('patient'));
    $patient->load('visits.procedures', 'toothRecords', 'appointments');
    $patient->load('visits.procedures', 'visits.invoice', 'toothRecords', 'appointments', 'invoices.payments');
}

    public function store(Request $request)
    {
        Patient::create($this->validated($request));

        return redirect()->route('patients.index')
            ->with('success', 'تمت إضافة المريض بنجاح');
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $patient->update($this->validated($request));

        return redirect()->route('patients.index')
            ->with('success', 'تم تحديث بيانات المريض');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'تم حذف المريض');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'allergies' => ['nullable', 'string'],
            'medical_history' => ['nullable', 'string'],
        ], [], [
            'name' => 'الاسم',
            'phone' => 'رقم الهاتف',
            'gender' => 'الجنس',
            'birth_date' => 'تاريخ الميلاد',
            'address' => 'العنوان',
            'allergies' => 'الحساسية',
            'medical_history' => 'التاريخ المرضي',
        ]);
    }
}