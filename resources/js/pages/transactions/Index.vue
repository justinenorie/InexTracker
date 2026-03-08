<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import CreateTransaction from '@/pages/transactions/CreateTransaction.vue';
import DeleteTransaction from '@/pages/transactions/DeleteTransaction.vue';
import EditTransaction from '@/pages/transactions/EditTransaction.vue';
import { index } from '@/routes/transactions';
import type { BreadcrumbItem } from '@/types';

type Category = {
    id: string;
    name: string;
    type: string;
    color?: string | null;
};

type Transaction = {
    id: string;
    type: 'income' | 'expense' | string;
    amount: string;
    description?: string | null;
    transacted_at: string;
    category?: Category | null;
    category_id?: string | null;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Paginator<T> = {
    data: T[];
    links: PaginationLink[];
};

type Props = {
    filters: {
        type?: string | null;
        category_id?: string | number | null;
        from?: string | null;
        to?: string | null;
        search?: string | null;
    };
    categories: Category[];
    transactions: Paginator<Transaction>;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Transactions', href: index() },
];

const typeValue = ref<string>(props.filters.type ?? '');
const categoryValue = ref<string>(
    props.filters.category_id ? String(props.filters.category_id) : '',
);
const fromValue = ref<string>(props.filters.from ?? '');
const toValue = ref<string>(props.filters.to ?? '');
const searchValue = ref<string>(props.filters.search ?? '');

watch(
    () => props.filters,
    (f) => {
        typeValue.value = f.type ?? '';
        categoryValue.value = f.category_id ? String(f.category_id) : '';
        fromValue.value = f.from ?? '';
        toValue.value = f.to ?? '';
        searchValue.value = f.search ?? '';
    },
);

const apply = () => {
    router.get(
        index.url({
            query: {
                type: typeValue.value || undefined,
                category_id: categoryValue.value || undefined,
                from: fromValue.value || undefined,
                to: toValue.value || undefined,
                search: searchValue.value || undefined,
            },
        }),
        {},
        { preserveScroll: true, preserveState: true },
    );
};

const clear = () => {
    typeValue.value = '';
    categoryValue.value = '';
    fromValue.value = '';
    toValue.value = '';
    searchValue.value = '';
    apply();
};

const formatMoney = (value: string) => {
    const n = Number(value);
    if (Number.isNaN(n)) return value;
    return new Intl.NumberFormat(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(n);
};
</script>

<template>
    <Head title="Transactions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <Heading
                title="Transactions"
                description="Browse and filter your income and expenses."
            />

            <div class="rounded-xl border bg-card p-4">
                <div class="grid gap-3 md:grid-cols-5">
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs text-muted-foreground"
                            >Search</label
                        >
                        <Input
                            v-model="searchValue"
                            placeholder="Description or category..."
                            @keyup.enter="apply"
                        />
                    </div>

                    <div>
                        <label class="mb-1 block text-xs text-muted-foreground"
                            >Type</label
                        >
                        <select
                            v-model="typeValue"
                            class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        >
                            <option value="">All</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs text-muted-foreground"
                            >Category</label
                        >
                        <select
                            v-model="categoryValue"
                            class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        >
                            <option value="">All</option>
                            <option
                                v-for="c in props.categories"
                                :key="c.id"
                                :value="String(c.id)"
                            >
                                {{ c.name }}
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label
                                class="mb-1 block text-xs text-muted-foreground"
                                >From</label
                            >
                            <Input v-model="fromValue" type="date" />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-xs text-muted-foreground"
                                >To</label
                            >
                            <Input v-model="toValue" type="date" />
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                    <Button type="button" @click="apply">Apply</Button>
                    <Button type="button" variant="outline" @click="clear"
                        >Clear</Button
                    >
                </div>
            </div>

            <div class="rounded-xl border bg-card p-4">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <div class="text-sm font-medium">Results</div>
                    <div class="flex items-center gap-3">
                        <div class="text-xs text-muted-foreground">
                            Showing {{ props.transactions.data.length }} item(s)
                        </div>
                        <CreateTransaction :categories="props.categories" />
                    </div>
                </div>

                <div
                    v-if="props.transactions.data.length === 0"
                    class="text-sm text-muted-foreground"
                >
                    No transactions found.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full min-w-[820px] text-sm">
                        <thead class="text-xs text-muted-foreground">
                            <tr>
                                <th class="px-2 py-2 text-left font-medium">
                                    Date
                                </th>
                                <th class="px-2 py-2 text-left font-medium">
                                    Type
                                </th>
                                <th class="px-2 py-2 text-left font-medium">
                                    Category
                                </th>
                                <th class="px-2 py-2 text-right font-medium">
                                    Amount
                                </th>
                                <th class="px-2 py-2 text-left font-medium">
                                    Description
                                </th>
                                <th class="px-2 py-2 text-right font-medium">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="t in props.transactions.data"
                                :key="t.id"
                                class="hover:bg-muted/50"
                            >
                                <td class="px-2 py-2 whitespace-nowrap">
                                    {{ t.transacted_at }}
                                </td>
                                <td class="px-2 py-2">
                                    <Badge
                                        :variant="
                                            t.type === 'income'
                                                ? 'default'
                                                : 'secondary'
                                        "
                                        class="capitalize"
                                    >
                                        {{ t.type }}
                                    </Badge>
                                </td>
                                <td class="px-2 py-2">
                                    {{ t.category?.name ?? '—' }}
                                </td>
                                <td class="px-2 py-2 text-right tabular-nums">
                                    {{ formatMoney(t.amount) }}
                                </td>
                                <td class="px-2 py-2">
                                    {{ t.description ?? '—' }}
                                </td>
                                <td class="px-2 py-2">
                                    <div class="flex justify-end gap-2">
                                        <EditTransaction
                                            :transaction="t"
                                            :categories="props.categories"
                                        />
                                        <DeleteTransaction :transaction="t" />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <Pagination :links="props.transactions.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
