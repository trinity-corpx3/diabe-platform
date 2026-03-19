<?php

namespace App\Utils;

use Carbon\Carbon;

class PaymentTerms
{
    public const MONTH_OFFSET = 100;

    public static function defaultTerms(): array
    {
        return [
            0,
            7,
            10,
            14,
            15,
            101,
            102,
            103,
            104,
            105,
            106,
            107,
            108,
            109,
            110,
            111,
            112,
        ];
    }

    public static function isMonthlyTerm(null|int|string $paymentTerms): bool
    {
        if ($paymentTerms === null || $paymentTerms === '') {
            return false;
        }

        return (int) $paymentTerms > self::MONTH_OFFSET;
    }

    public static function getMonths(null|int|string $paymentTerms): int
    {
        if (! self::isMonthlyTerm($paymentTerms)) {
            return 0;
        }

        return max(0, ((int) $paymentTerms) - self::MONTH_OFFSET);
    }

    public static function getProjectPaymentDay(mixed $project): ?int
    {
        if (! $project || ! isset($project->custom_value4) || $project->custom_value4 === '') {
            return null;
        }

        $day = (int) $project->custom_value4;

        if ($day < 1 || $day > 31) {
            return null;
        }

        return $day;
    }

    public static function resolveDueDate(Carbon|string $date, null|int|string $paymentTerms, mixed $project = null): ?Carbon
    {
        if ($paymentTerms === null || $paymentTerms === '') {
            return null;
        }

        $baseDate = $date instanceof Carbon ? $date->copy() : Carbon::parse($date);

        if (! self::isMonthlyTerm($paymentTerms)) {
            return $baseDate->addDays((int) $paymentTerms);
        }

        $months = self::getMonths($paymentTerms);
        $targetDate = $baseDate->copy()->addMonthsNoOverflow($months);
        $paymentDay = self::getProjectPaymentDay($project);

        if (! $paymentDay) {
            return $targetDate;
        }

        $lastDayOfMonth = $targetDate->copy()->endOfMonth()->day;

        return $targetDate->copy()->day(min($paymentDay, $lastDayOfMonth));
    }

    public static function getLabel(null|int|string $paymentTerms): string
    {
        if ($paymentTerms === null || $paymentTerms === '') {
            return ctrans('texts.payment_terms_net') . ' 0';
        }

        if (! self::isMonthlyTerm($paymentTerms)) {
            $days = (int) $paymentTerms;

            return ctrans('texts.payment_terms_net') . ' ' . ($days === -1 ? 0 : $days);
        }

        $months = self::getMonths($paymentTerms);
        $suffix = ctrans('texts.month');
  
        return $months . ' ' . $suffix;
    }
}
