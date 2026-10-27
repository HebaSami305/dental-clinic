<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Carbon\Carbon;

class AppointmentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:appointments')];
        }
    public function index(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date) : today();

        $appointments = Appointment::with('patient')
            ->onDate($date)
            ->orderBy('starts_at')
            ->get();

        return view('appointments.index', compact('appointments', 'date'));
    }

    public function create(Request $request)
    {
        return view('appointments.create', [
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'file_number']),
            'selectedPatient' => $request->patient_id,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $this->ensureNoConflict($data['starts_at'], $data['duration_minutes']);

        $appointment = Appointment::create($data);

        return redirect()
            ->route('appointments.index', ['date' => $appointment->starts_at->toDateString()])
            ->with('success', 'تم حجز الموعد');
    }

    public function edit(Appointment $appointment)
    {
        return view('appointments.edit', [
            'appointment' => $appointment,
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'file_number']),
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $this->validated($request, withStatus: true);
        $this->ensureNoConflict($data['starts_at'], $data['duration_minutes'], $appointment->id, $data['status']);

        $appointment->update($data);

        return redirect()
            ->route('appointments.index', ['date' => $appointment->starts_at->toDateString()])
            ->with('success', 'تم تحديث الموعد');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Appointment::STATUSES))],
        ]);

        $appointment->update($data);

        return back()->with('success', 'تم تحديث حالة الموعد');
    }

    public function destroy(Appointment $appointment)
    {
        $date = $appointment->starts_at->toDateString();
        $appointment->delete();

        return redirect()
            ->route('appointments.index', ['date' => $date])
            ->with('success', 'تم حذف الموعد');
    }

    private function validated(Request $request, bool $withStatus = false): array
    {
        $rules = [
            'patient_id' => ['required', 'exists:patients,id'],
            'starts_at' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:240'],
            'type' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];

        if ($withStatus) {
            $rules['status'] = ['required', Rule::in(array_keys(Appointment::STATUSES))];
        }

        return $request->validate($rules, [], [
            'patient_id' => 'المريض',
            'starts_at' => 'وقت الموعد',
            'duration_minutes' => 'المدة',
            'type' => 'نوع الإجراء',
            'notes' => 'الملاحظات',
            'status' => 'الحالة',
        ]);
    }

    /**
     * يمنع تداخل موعدين غير ملغيين بنفس الوقت.
     */
    private function ensureNoConflict(string $startsAt, int $duration, ?int $ignoreId = null, ?string $status = null): void
    {
        if ($status === 'cancelled') {
            return;
        }

        $start = Carbon::parse($startsAt);
        $end = $start->copy()->addMinutes($duration);

        $conflict = Appointment::with('patient')
            ->where('status', '!=', 'cancelled')
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('starts_at', '<', $end)
            ->whereRaw('DATE_ADD(starts_at, INTERVAL duration_minutes MINUTE) > ?', [$start])
            ->first();

        if ($conflict) {
            throw ValidationException::withMessages([
                'starts_at' => 'يوجد موعد متداخل: ' . $conflict->patient->name
    . ' من ' . $conflict->starts_at->format('H:i')
    . ' إلى ' . $conflict->ends_at->format('H:i'),
            ]);
        }
    }
}