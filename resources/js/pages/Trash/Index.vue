<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { RefreshCw, Trash, Trash2 } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import categoriesRoutes from '@/routes/categories';
import transactionsRoutes from '@/routes/transactions';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface Category {
    id: string;
    name: string;
    type: string;
    color: string;
    deleted_at: string;
}

interface Transaction {
    id: string;
    amount: string;
    description: string;
    type: string;
    transacted_at: string;
    deleted_at: string;
    category?: {
        name: string;
    };
}

defineProps<{
    transactions: Transaction[];
    categories: Category[];
}>();

const restoreCategory = (id: string) => {
    router.post(categoriesRoutes.restore(id).url, {}, { preserveScroll: true });
};

const forceDeleteCategory = (id: string) => {
    if (confirm('Are you sure you want to permanently delete this category?')) {
        router.delete(categoriesRoutes.forceDelete(id).url, {
            preserveScroll: true,
        });
    }
};

const restoreTransaction = (id: string) => {
    router.post(
        transactionsRoutes.restore(id).url,
        {},
        { preserveScroll: true },
    );
};

const forceDeleteTransaction = (id: string) => {
    if (
        confirm('Are you sure you want to permanently delete this transaction?')
    ) {
        router.delete(transactionsRoutes.forceDelete(id).url, {
            preserveScroll: true,
        });
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString();
};
</script>

<template>
    <Head title="Trash" />

    <AppLayout>
        <div class="space-y-6 p-6">
            <div class="flex items-center gap-2">
                <Trash2 class="h-6 w-6" />
                <h1 class="text-2xl font-bold">Trash</h1>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Deleted Categories</CardTitle>
                    <CardDescription>
                        Restoring a category will also restore its associated
                        transactions.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Name</TableHead>
                                <TableHead>Type</TableHead>
                                <TableHead>Deleted At</TableHead>
                                <TableHead class="text-right"
                                    >Actions</TableHead
                                >
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="category in categories"
                                :key="category.id"
                            >
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <div
                                            :style="{
                                                backgroundColor: category.color,
                                            }"
                                            class="h-3 w-3 rounded-full"
                                        ></div>
                                        {{ category.name }}
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        :variant="
                                            category.type === 'expense'
                                                ? 'destructive'
                                                : 'default'
                                        "
                                    >
                                        {{ category.type }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{
                                    formatDate(category.deleted_at)
                                }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="
                                                restoreCategory(category.id)
                                            "
                                        >
                                            <RefreshCw class="mr-2 h-4 w-4" />
                                            Restore
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="
                                                forceDeleteCategory(category.id)
                                            "
                                        >
                                            <Trash class="mr-2 h-4 w-4" />
                                            Delete
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="categories.length === 0">
                                <TableCell
                                    colspan="4"
                                    class="py-10 text-center text-muted-foreground"
                                >
                                    No deleted categories found.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Deleted Transactions</CardTitle>
                    <CardDescription>
                        Transactions that were individually deleted.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Date</TableHead>
                                <TableHead>Description</TableHead>
                                <TableHead>Category</TableHead>
                                <TableHead>Amount</TableHead>
                                <TableHead>Deleted At</TableHead>
                                <TableHead class="text-right"
                                    >Actions</TableHead
                                >
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="transaction in transactions"
                                :key="transaction.id"
                            >
                                <TableCell>{{
                                    formatDate(transaction.transacted_at)
                                }}</TableCell>
                                <TableCell>{{
                                    transaction.description
                                }}</TableCell>
                                <TableCell>{{
                                    transaction.category?.name || 'N/A'
                                }}</TableCell>
                                <TableCell
                                    :class="
                                        transaction.type === 'expense'
                                            ? 'text-red-500'
                                            : 'text-green-500'
                                    "
                                >
                                    {{ transaction.amount }}
                                </TableCell>
                                <TableCell>{{
                                    formatDate(transaction.deleted_at)
                                }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="
                                                restoreTransaction(
                                                    transaction.id,
                                                )
                                            "
                                        >
                                            <RefreshCw class="mr-2 h-4 w-4" />
                                            Restore
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="
                                                forceDeleteTransaction(
                                                    transaction.id,
                                                )
                                            "
                                        >
                                            <Trash class="mr-2 h-4 w-4" />
                                            Delete
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="transactions.length === 0">
                                <TableCell
                                    colspan="6"
                                    class="py-10 text-center text-muted-foreground"
                                >
                                    No deleted transactions found.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
