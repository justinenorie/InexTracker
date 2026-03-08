<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Chart from '@/components/Chart.vue';
import DateRangeBar from '@/components/DateRangeBar.vue';
import Heading from '@/components/Heading.vue';
import PieChart from '@/components/PieChart.vue';
import StatCard from '@/components/StatCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import CreateTransaction from '@/pages/transactions/CreateTransaction.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

type Totals = {
    total_income: string;
    total_expense: string;
};

type Category = {
    id: string;
    name: string;
    type: string;
    color?: string | null;
};

type TotalsByCategoryRow = {
    category_id: string;
    category_name: string;
    category_color: string;
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
    history: {
        period: string;
        label: string;
        income: number;
        expense: number;
    }[];
    categories: Category[];
};

const props = defineProps<Props>();

const page = usePage();
const user = computed(() => page.props.auth.user as any);

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
        <div class="space-y-6 overflow-x-auto p-6">
            <div
                class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-center"
            >
                <Heading
                    title="Dashboard"
                    description="Track totals and category breakdown. Use the date range to focus your reporting."
                />
                <CreateTransaction
                    :categories="props.categories"
                    class="w-full sm:w-auto"
                />
            </div>

            <DateRangeBar
                :from="props.filters.from ?? null"
                :to="props.filters.to ?? null"
                :submit-url="
                    ({ from, to }) => dashboard.url({ query: { from, to } })
                "
            />

            <div class="grid gap-4 md:grid-cols-3">
                <StatCard
                    label="Current Balance"
                    :value="formatMoney(user.balance)"
                />
                <StatCard
                    label="Total Income"
                    :value="formatMoney(props.totals.total_income)"
                    variant="success"
                />
                <StatCard
                    label="Total Expenses"
                    :value="formatMoney(props.totals.total_expense)"
                    variant="destructive"
                />
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
                <Chart class="lg:col-span-3" :data="props.history" />
                <PieChart
                    class="lg:col-span-2"
                    :data="props.totalsByCategory"
                />
            </div>
        </div>
    </AppLayout>
</template>
