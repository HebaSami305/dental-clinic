<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\ToothRecord;
use App\Models\User;
use App\Models\VisitProcedure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
    ['admin@admin.com',      'مدير النظام',  'admin'],
    ['doctor@clinic.com',    'د. خالد',      'doctor'],
    ['secretary@clinic.com', 'سارة',         'secretary'],
    ['accountant@clinic.com','محمود',        'accountant'],
] as [$email, $name, $role]) {
    User::updateOrCreate(
        ['email' => $email],
        ['name' => $name, 'role' => $role, 'password' => Hash::make('password')]
    );
}

        $patients = Patient::factory(30)->create();

        $catalog = collect(VisitProcedure::CATALOG)->except('أخرى');
        $teeth = ToothRecord::allTeeth();
        $withoutTooth = ['كشف', 'تنظيف', 'تقويم'];

        // زيارات وفواتير ودفعات لـ 20 مريض
        $patients->random(20)->each(function (Patient $patient) use ($catalog, $teeth, $withoutTooth) {
            foreach (range(1, rand(1, 3)) as $_) {
                $date = now()->subDays(rand(0, 60));

                $visit = $patient->visits()->create([
                    'visited_at' => $date,
                    'diagnosis' => fake()->randomElement(['تسوس', 'التهاب لثة', 'فحص دوري', 'ألم بالضرس', null]),
                ]);

                $procedures = collect(range(1, rand(1, 3)))->map(function () use ($catalog, $teeth, $withoutTooth) {
                    $name = $catalog->keys()->random();

                    return [
                        'name' => $name,
                        'tooth_number' => in_array($name, $withoutTooth) ? null : fake()->randomElement($teeth),
                        'price' => $catalog[$name]['price'],
                    ];
                });

                $visit->procedures()->createMany($procedures->all());

                // مخطط الأسنان
                foreach ($procedures as $p) {
                    $condition = $catalog[$p['name']]['condition'];

                    if ($p['tooth_number'] && $condition) {
                        $patient->toothRecords()->updateOrCreate(
                            ['tooth_number' => $p['tooth_number']],
                            ['condition' => $condition]
                        );
                    }
                }

                // الفاتورة
                $subtotal = (float) $procedures->sum('price');
                $discount = rand(0, 4) === 0 ? round($subtotal * 0.1, 2) : 0;
                $total = $subtotal - $discount;

                $invoice = Invoice::create([
                    'patient_id' => $patient->id,
                    'visit_id' => $visit->id,
                    'issued_at' => $date,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                ]);

                $invoice->items()->createMany(
                    $procedures->map(fn ($p) => [
                        'tooth_number' => $p['tooth_number'],
                        'description' => $p['name'],
                        'price' => $p['price'],
                    ])->all()
                );

                // الدفعات: 20% بدون دفع، 40% جزئي، 40% كامل
                $roll = rand(1, 10);
                $amount = $roll <= 2 ? 0 : ($roll <= 6 ? round($total * 0.5, 2) : $total);

                if ($amount > 0) {
                    $invoice->payments()->create([
                        'amount' => $amount,
                        'method' => array_rand(Payment::METHODS),
                        'paid_at' => $date,
                    ]);
                }
            }
        });

        // مواعيد اليوم (بدون تداخل)
        foreach (range(0, 5) as $i) {
            Appointment::create([
                'patient_id' => $patients->random()->id,
                'starts_at' => today()->setTime(9, 0)->addMinutes($i * 45),
                'duration_minutes' => 30,
                'type' => fake()->randomElement(Appointment::TYPES),
                'status' => $i < 2 ? 'completed' : ($i === 2 ? 'in_progress' : 'scheduled'),
            ]);
        }

        // مواعيد قادمة
        foreach (range(0, 8) as $i) {
            Appointment::create([
                'patient_id' => $patients->random()->id,
                'starts_at' => today()->addDays(1 + intdiv($i, 3))->setTime(9 + ($i % 3) * 2, 0),
                'duration_minutes' => 30,
                'type' => fake()->randomElement(Appointment::TYPES),
                'status' => 'scheduled',
            ]);
        }
    }
}
