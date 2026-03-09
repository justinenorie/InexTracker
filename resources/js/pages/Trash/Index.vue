<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { RefreshCw } from 'lucide-vue-next'
import DeleteConfirmationModal from '@/components/DeleteConfirmationModal.vue'
import Heading from '@/components/Heading.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { useCurrency } from '@/composables/useCurrency'
import { useToast } from '@/composables/useToast'
import AppLayout from '@/layouts/AppLayout.vue'
import categoriesRoutes from '@/routes/categories'
import transactionsRoutes from '@/routes/transactions'
import { trash } from '@/routes/transactions'
import type { BreadcrumbItem, Category, Transaction } from '@/types'

defineProps<{
  transactions: Transaction[]
  categories: Category[]
}>()

const { formatMoney } = useCurrency()
const { success } = useToast()

const restoreCategory = (id: string) => {
  router.post(
    categoriesRoutes.restore(id).url,
    {},
    {
      preserveScroll: true,
      onSuccess: () => success('Category restored successfully'),
    },
  )
}

const forceDeleteCategory = (id: string) => {
  router.delete(categoriesRoutes.forceDelete(id).url, {
    preserveScroll: true,
    onSuccess: () => success('Category deleted permanently'),
  })
}

const restoreTransaction = (id: string) => {
  router.post(
    transactionsRoutes.restore(id).url,
    {},
    {
      preserveScroll: true,
      onSuccess: () => success('Transaction restored successfully'),
    },
  )
}

const forceDeleteTransaction = (id: string) => {
  router.delete(transactionsRoutes.forceDelete(id).url, {
    preserveScroll: true,
    onSuccess: () => success('Transaction deleted permanently'),
  })
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Trash',
    href: trash(),
  },
]

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString()
}
</script>

<template>
  <Head title="Trash" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
      <Heading
        title="Trash"
        description="Restoring an item will move it back to its original location. Deleting an item permanently will remove it from the database and cannot be undone."
      />

      <Card>
        <CardHeader>
          <CardTitle>Deleted Categories</CardTitle>
          <CardDescription>
            Restoring a category will also restore its associated transactions.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>Type</TableHead>
                <TableHead>Deleted At</TableHead>
                <TableHead class="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="category in categories" :key="category.id">
                <TableCell>
                  <div class="flex items-center gap-2">
                    <div
                      :style="{
                        backgroundColor: category.color ?? '',
                      }"
                    ></div>
                    {{ category.name }}
                  </div>
                </TableCell>
                <TableCell>
                  <Badge
                    :variant="
                      category.type === 'income' ? 'success' : 'destructive'
                    "
                    class="capitalize"
                  >
                    {{ category.type }}
                  </Badge>
                </TableCell>
                <TableCell>{{
                  formatDate(category.deleted_at ?? '')
                }}</TableCell>
                <TableCell class="text-right">
                  <div class="flex justify-end gap-2">
                    <Button
                      variant="outline"
                      size="sm"
                      @click="restoreCategory(category.id)"
                    >
                      <RefreshCw class="mr-2 h-4 w-4" />
                      Restore
                    </Button>
                    <DeleteConfirmationModal
                      title="Permanently delete category?"
                      description="This action cannot be undone. All associated transactions will also be permanently deleted."
                      @confirm="forceDeleteCategory(category.id)"
                    />
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
                <TableHead class="text-right">Actions</TableHead>
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
                <TableCell>{{ transaction.description }}</TableCell>
                <TableCell>{{ transaction.category?.name || 'N/A' }}</TableCell>
                <TableCell
                  class="text-right font-medium tabular-nums"
                  :class="
                    transaction.type === 'expense'
                      ? 'text-destructive'
                      : 'text-success'
                  "
                >
                  {{ formatMoney(transaction.amount) }}
                </TableCell>
                <TableCell>{{
                  formatDate(transaction.deleted_at ?? '')
                }}</TableCell>
                <TableCell class="text-right">
                  <div class="flex justify-end gap-2">
                    <Button
                      variant="outline"
                      size="sm"
                      @click="restoreTransaction(transaction.id)"
                    >
                      <RefreshCw class="mr-2 h-4 w-4" />
                      Restore
                    </Button>
                    <DeleteConfirmationModal
                      title="Permanently delete transaction?"
                      description="This action cannot be undone and the transaction record will be lost forever."
                      @confirm="forceDeleteTransaction(transaction.id)"
                    />
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
