<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Résout la période sélectionnée sur le tableau de bord (filtre en haut de
 * page) à partir des paramètres de requête `period`/`start`/`end`.
 */
final class DashboardPeriod
{
    private function __construct(
        public readonly Carbon $start,
        public readonly Carbon $end,
        public readonly string $key,
        public readonly string $label,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $key = $request->string('period')->toString();

        return match ($key) {
            'today' => self::today(),
            'yesterday' => self::yesterday(),
            'week' => self::week(),
            'year' => self::year(),
            'custom' => self::custom($request->string('start')->toString(), $request->string('end')->toString()),
            default => self::month(),
        };
    }

    private static function today(): self
    {
        return new self(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), 'today', "Aujourd'hui");
    }

    private static function yesterday(): self
    {
        $day = Carbon::yesterday();

        return new self($day->copy()->startOfDay(), $day->copy()->endOfDay(), 'yesterday', 'Hier');
    }

    private static function week(): self
    {
        return new self(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek(), 'week', 'Cette semaine');
    }

    private static function month(): self
    {
        return new self(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth(), 'month', 'Ce mois');
    }

    private static function year(): self
    {
        return new self(Carbon::now()->startOfYear(), Carbon::now()->endOfYear(), 'year', 'Cette année');
    }

    /**
     * Période couvrant tout l'historique — utilisée par la page Rapports
     * pour obtenir les indicateurs "période" de DashboardService::getStats()
     * sur l'ensemble des données, sans filtre de date.
     */
    public static function allTime(): self
    {
        return new self(Carbon::create(2000, 1, 1)->startOfDay(), Carbon::now()->endOfDay(), 'all', 'Toutes les périodes');
    }

    private static function custom(string $rawStart, string $rawEnd): self
    {
        try {
            $start = Carbon::createFromFormat('Y-m-d', $rawStart)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $rawEnd)->endOfDay();
        } catch (\Throwable) {
            return self::month();
        }

        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $label = 'Du '.$start->format('d/m/Y').' au '.$end->format('d/m/Y');

        return new self($start, $end, 'custom', $label);
    }

    /**
     * Paramètres de requête permettant de reconstruire l'URL courante
     * (utilisé côté JS pour `history.pushState`).
     */
    public function toQueryParams(): array
    {
        if ($this->key !== 'custom') {
            return ['period' => $this->key];
        }

        return [
            'period' => 'custom',
            'start' => $this->start->toDateString(),
            'end' => $this->end->toDateString(),
        ];
    }
}
