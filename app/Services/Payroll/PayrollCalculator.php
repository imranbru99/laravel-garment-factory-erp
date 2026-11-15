<?php

namespace App\Services\Payroll;

use App\Models\Employee;
use App\Models\PayrollLine;
use App\Models\PayrollPeriod;
use App\Models\ProductionLog;
use App\Models\WorkerAttendance;

class PayrollCalculator
{
    public function build(PayrollPeriod $period): void
    {
        $employees = Employee::query()->where('factory_id', $period->factory_id)->where('is_active', true)->get();

        foreach ($employees as $employee) {
            $presentDays = WorkerAttendance::query()
                ->where('employee_id', $employee->id)
                ->where('direction', 'in')
                ->whereBetween('punched_at', [$period->start_date, $period->end_date->copy()->endOfDay()])
                ->count();

            $pieces = (int) ProductionLog::query()
                ->where('employee_id', $employee->id)
                ->whereBetween('log_date', [$period->start_date, $period->end_date])
                ->sum('actual_qty');

            $pieceAmount = round($pieces * (float) $employee->piece_rate, 2);
            $attendanceAmount = round($presentDays * 8 * (float) $employee->hourly_rate, 2);

            PayrollLine::query()->updateOrCreate(
                [
                    'payroll_period_id' => $period->id,
                    'employee_id' => $employee->id,
                ],
                [
                    'present_days' => $presentDays,
                    'pieces' => $pieces,
                    'piece_amount' => $pieceAmount,
                    'attendance_amount' => $attendanceAmount,
                    'gross' => round($pieceAmount + $attendanceAmount, 2),
                ]
            );
        }
    }
}
