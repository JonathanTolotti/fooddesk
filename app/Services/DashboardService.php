<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Table;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    /**
     * Get today's statistics (revenue, orders, average ticket, service fee)
     */
    public function getTodayStats(): array
    {
        $closedOrders = Order::where('status', 'closed')
            ->whereDate('opened_at', today())
            ->get();

        $ordersCount = $closedOrders->count();
        $revenue = (float) $closedOrders->sum('total');
        $serviceFee = (float) $closedOrders->sum('service_fee');

        return [
            'revenue' => $revenue,
            'orders_count' => $ordersCount,
            'average_ticket' => $ordersCount > 0 ? $revenue / $ordersCount : 0,
            'service_fee' => $serviceFee,
        ];
    }

    /**
     * Get current real-time situation
     */
    public function getCurrentSituation(): array
    {
        $openOrders = Order::where('status', 'open')->get();

        return [
            'open_orders' => $openOrders->count(),
            'open_orders_value' => (float) $openOrders->sum('total'),
            'occupied_tables' => Table::where('status', 'occupied')->count(),
            'total_tables' => Table::where('is_active', true)->count(),
            'kitchen_items' => OrderItem::whereIn('status', ['pending', 'preparing'])->count(),
        ];
    }

    /**
     * Get top selling products of the day with cache
     */
    public function getTopProducts(int $limit = 5): array
    {
        return Cache::remember('dashboard.top_products.' . today()->format('Y-m-d'), 300, function () use ($limit) {
            return OrderItem::query()
                ->whereHas('order', fn ($q) => $q->whereDate('opened_at', today()))
                ->where('status', '!=', 'cancelled')
                ->selectRaw('product_name, SUM(quantity) as total_sold')
                ->groupBy('product_name')
                ->orderByDesc('total_sold')
                ->limit($limit)
                ->get()
                ->map(fn ($item) => [
                    'name' => $item->product_name,
                    'total_sold' => (int) $item->total_sold,
                ])
                ->toArray();
        });
    }

    /**
     * Get payments grouped by method
     */
    public function getPaymentsByMethod(): array
    {
        $payments = OrderPayment::query()
            ->whereHas('order', fn ($q) => $q
                ->where('status', 'closed')
                ->whereDate('opened_at', today()))
            ->selectRaw('method, SUM(amount) as total')
            ->groupBy('method')
            ->get();

        $methods = ['credit_card', 'debit_card', 'cash', 'pix'];
        $result = [];

        foreach ($methods as $method) {
            $payment = $payments->firstWhere('method', $method);
            $result[$method] = [
                'total' => $payment ? (float) $payment->total : 0,
                'label' => $this->getMethodLabel($method),
                'icon' => $this->getMethodIcon($method),
            ];
        }

        return $result;
    }

    /**
     * Get all dashboard data
     */
    public function getAllData(): array
    {
        return [
            'today_stats' => $this->getTodayStats(),
            'current_situation' => $this->getCurrentSituation(),
            'top_products' => $this->getTopProducts(),
            'payments_by_method' => $this->getPaymentsByMethod(),
        ];
    }

    private function getMethodLabel(string $method): string
    {
        return match ($method) {
            'credit_card' => 'Cartao de Credito',
            'debit_card' => 'Cartao de Debito',
            'cash' => 'Dinheiro',
            'pix' => 'PIX',
            default => $method,
        };
    }

    private function getMethodIcon(string $method): string
    {
        return match ($method) {
            'credit_card', 'debit_card' => 'credit-card',
            'cash' => 'banknotes',
            'pix' => 'qr-code',
            default => 'currency-dollar',
        };
    }
}
