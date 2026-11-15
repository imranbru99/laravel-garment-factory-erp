<?php

namespace App\Services\Calendar;

use App\Models\FactoryHoliday;
use Carbon\CarbonInterface;
use Tarikh\Tarikh;

class FactoryCalendar
{
    public function format(CarbonInterface|string $date, string $locale = 'en'): string
    {
        $instant = $date instanceof CarbonInterface ? $date->toDateTimeImmutable() : $date;

        return Tarikh::parse($instant, config('factory.timezone', 'Asia/Dhaka'))
            ->format($locale === 'bn' ? 'd/m/Y' : 'd M Y');
    }

    public function isHoliday(CarbonInterface|string $date, ?int $factoryId = null): bool
    {
        $day = Tarikh::parse(
            $date instanceof CarbonInterface ? $date->toDateTimeImmutable() : $date,
            config('factory.timezone', 'Asia/Dhaka')
        )->toDateString();

        return FactoryHoliday::query()
            ->whereDate('holiday_date', $day)
            ->where(function ($query) use ($factoryId) {
                $query->whereNull('factory_id');
                if ($factoryId) {
                    $query->orWhere('factory_id', $factoryId);
                }
            })
            ->exists();
    }

    public function workingDay(CarbonInterface $date, ?int $factoryId = null): bool
    {
        return ! $date->isFriday() && ! $this->isHoliday($date, $factoryId);
    }
}
