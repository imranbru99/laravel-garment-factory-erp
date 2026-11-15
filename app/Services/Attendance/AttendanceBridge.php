<?php

namespace App\Services\Attendance;

use App\Models\Employee;
use App\Models\WorkerAttendance;
use Carbon\CarbonInterface;
use ImranDevBd\AttendanceHub\DTOs\DeviceConnection;
use ImranDevBd\AttendanceHub\Facades\AttendanceHub;
use Throwable;

class AttendanceBridge
{
    public function punch(
        Employee $employee,
        string $direction,
        string $source = 'manual',
        ?CarbonInterface $at = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $deviceName = null,
    ): WorkerAttendance {
        return WorkerAttendance::query()->create([
            'factory_id' => $employee->factory_id,
            'employee_id' => $employee->id,
            'punched_at' => $at ?? now(),
            'source' => $source,
            'direction' => $direction,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'device_name' => $deviceName,
        ]);
    }

    /**
     * Pull punches from a biometric or virtual device and map them onto employees
     * by employee code. GPS/QR devices use the virtual driver.
     *
     * @param  array<string, mixed>  $connection
     */
    public function pull(string $provider, array $connection, ?CarbonInterface $since = null): int
    {
        $driver = AttendanceHub::provider($provider);
        $driver->connect(DeviceConnection::fromArray($connection));
        $punches = $driver->pullAttendanceLogs($since);
        $stored = 0;

        foreach ($punches as $punch) {
            $code = $punch->employeeId ?: $punch->deviceUserId;
            $employee = Employee::query()->where('employee_code', $code)->first();

            if (! $employee) {
                continue;
            }

            $direction = str_contains(strtolower($punch->punchType->value ?? 'in'), 'out') ? 'out' : 'in';
            $exists = WorkerAttendance::query()
                ->where('employee_id', $employee->id)
                ->where('punched_at', $punch->punchedAt)
                ->exists();

            if ($exists) {
                continue;
            }

            $this->punch(
                employee: $employee,
                direction: $direction,
                source: $provider === 'virtual' ? 'qr' : 'biometric',
                at: $punch->punchedAt,
                latitude: isset($punch->location['lat']) ? (float) $punch->location['lat'] : null,
                longitude: isset($punch->location['lng']) ? (float) $punch->location['lng'] : null,
                deviceName: $provider,
            );
            $stored++;
        }

        return $stored;
    }

    public function safePull(string $provider, array $connection, ?CarbonInterface $since = null): int
    {
        try {
            return $this->pull($provider, $connection, $since);
        } catch (Throwable) {
            return 0;
        }
    }
}
