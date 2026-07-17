<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionStatus;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonInterface;
use Devhammed\LaravelBrickMoney\Money;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DashboardStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        return [
            Stat::make('Clients', $this->clientsCount($user))
                ->icon(Heroicon::OutlinedBuildingStorefront)
                ->color('primary'),

            Stat::make('Total Collected Today', $this->collectedAmount($user, now()->startOfDay(), now()->endOfDay()))
                ->icon(Heroicon::OutlinedBanknotes)
                ->color('success'),

            Stat::make('Total Collected This Month', $this->collectedAmount($user, now()->startOfMonth(), now()->endOfMonth()))
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('success'),
        ];
    }

    protected function clientsCount(User $user): int
    {
        if ($user->can('can_view_all_clients')) {
            return Client::count();
        }

        if ($user->can('can_view_client')) {
            return Client::whereHas('users', fn (Builder $query) => $query->whereKey($user->id))->count();
        }

        return 0;
    }

    protected function collectedAmount(User $user, CarbonInterface $from, CarbonInterface $to): string
    {
        $query = Transaction::query()
            ->where('status', TransactionStatus::SUCCESS->value)
            ->whereBetween('transaction_date_time', [$from, $to]);

        $this->scopeToPermittedClients($query, $user);

        $totals = $query
            ->selectRaw('currency, sum(amount) as total')
            ->groupBy('currency')
            ->get();

        return $this->formatTotals($totals);
    }

    protected function scopeToPermittedClients(Builder $query, User $user): void
    {
        if ($user->can('can_view_all_transactions')) {
            return;
        }

        if ($user->can('can_view_client')) {
            $query->whereHas('client.users', fn (Builder $query) => $query->whereKey($user->id));

            return;
        }

        $query->whereRaw('1 = 0');
    }

    protected function formatTotals(Collection $totals): string
    {
        if ($totals->isEmpty()) {
            return (string) Money::ofMinor(0, 'INR');
        }

        return $totals
            ->map(fn ($row) => (string) Money::ofMinor((int) $row->total, $row->currency))
            ->implode(' + ');
    }
}
