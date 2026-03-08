<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DateRangeBar from '@/components/DateRangeBar.vue';
import Heading from '@/components/Heading.vue';
import StatCard from '@/components/StatCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type Totals = {
    total_income: string;
    total_expense: string;
    revenue: string;
};

type TotalsByCategoryRow = {
    category_id: string;
    category_name: string;
    type: 'income' | 'expense' | string;
    total: string;
};

type Props = {
    filters: {
        from?: string | null;
        to?: string | null;
    };
    totals: Totals;
    totalsByCategory: TotalsByCategoryRow[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];

const formatMoney = (value: string | number) => {
    const n = typeof value === 'number' ? value : Number(value);
    if (Number.isNaN(n)) return String(value);
    return new Intl.NumberFormat(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(n);
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4">
            <Heading
                title="Income & Expense Overview"
                description="Track totals and category breakdown. Use the date range to focus your reporting."
            />

            <DateRangeBar
                :from="props.filters.from ?? null"
                :to="props.filters.to ?? null"
                :submit-url="
                    ({ from, to }) => dashboard.url({ query: { from, to } })
                "
            />

            <div class="grid gap-4 md:grid-cols-3">
                <StatCard
                    label="Total Income"
                    :value="formatMoney(props.totals.total_income)"
                />
                <StatCard
                    label="Total Expenses"
                    :value="formatMoney(props.totals.total_expense)"
                />
                <StatCard
                    label="Revenue"
                    :value="formatMoney(props.totals.revenue)"
                />
            </div>

            <div class="rounded-xl border bg-card p-4">
                <div class="mb-3 text-sm font-medium">Category Breakdown</div>
                <div
                    v-if="props.totalsByCategory.length === 0"
                    class="text-sm text-muted-foreground"
                >
                    No transactions found for the selected range.
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="w-full min-w-[520px] text-sm">
                        <thead class="text-xs text-muted-foreground">
                            <tr>
                                <th class="px-2 py-2 text-left font-medium">
                                    Category
                                </th>
                                <th class="px-2 py-2 text-left font-medium">
                                    Type
                                </th>
                                <th class="px-2 py-2 text-right font-medium">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="row in props.totalsByCategory"
                                :key="`${row.type}-${row.category_id}`"
                                class="hover:bg-muted/50"
                            >
                                <td class="px-2 py-2">
                                    {{ row.category_name }}
                                </td>
                                <td class="px-2 py-2 capitalize">
                                    {{ row.type }}
                                </td>
                                <td class="px-2 py-2 text-right tabular-nums">
                                    {{ formatMoney(row.total) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
