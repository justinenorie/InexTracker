<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { Download } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'
import Heading from '@/components/Heading.vue'
import Pagination from '@/components/Pagination.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import Button from '@/components/ui/button/Button.vue'
import Input from '@/components/ui/input/Input.vue'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { useCurrency } from '@/composables/useCurrency'
import AppLayout from '@/layouts/AppLayout.vue'
import CreateTransaction from '@/pages/transactions/CreateTransaction.vue'
import DeleteTransaction from '@/pages/transactions/DeleteTransaction.vue'
import EditTransaction from '@/pages/transactions/EditTransaction.vue'
import { index } from '@/routes/transactions'
import type { BreadcrumbItem, Category, Paginator, Transaction } from '@/types'

type Props = {
  filters: {
    type?: string | null
    category_id?: string | number | null
    from?: string | null
    to?: string | null
    search?: string | null
  }
  categories: Category[]
  transactions: Paginator<Transaction>
}

const props = defineProps<Props>()
const { formatMoney } = useCurrency()

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Transactions', href: index() }]

const typeValue = ref<string>(props.filters.type ?? 'all')
const categoryValue = ref<string>(
  props.filters.category_id ? String(props.filters.category_id) : 'all',
)
const searchValue = ref<string>(props.filters.search ?? '')

watch(
  () => props.filters,
  (f) => {
    typeValue.value = f.type ?? 'all'
    categoryValue.value = f.category_id ? String(f.category_id) : 'all'
    searchValue.value = f.search ?? ''
  },
)

const apply = () => {
  router.get(
    index.url({
      query: {
        type: typeValue.value !== 'all' ? typeValue.value : undefined,
        category_id:
          categoryValue.value !== 'all' ? categoryValue.value : undefined,
        search: searchValue.value || undefined,
      },
    }),
    {},
    { preserveScroll: true, preserveState: true },
  )
}

const clear = () => {
  typeValue.value = 'all'
  categoryValue.value = 'all'
  searchValue.value = ''
  apply()
}

const exportUrl = computed(() => {
  const params = new URLSearchParams()
  if (typeValue.value !== 'all') params.append('type', typeValue.value)
  if (categoryValue.value !== 'all')
    params.append('category_id', categoryValue.value)
  if (searchValue.value) params.append('search', searchValue.value)

  const queryString = params.toString()
  return `/data/export-transactions${queryString ? `?${queryString}` : ''}`
})

const formatDate = (date: string) => {
  return new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(date))
}
</script>

<template>
  <Head title="Transactions" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
      <Heading
        title="Transactions"
        description="Browse and filter your income and expenses."
      />

      <div class="rounded-xl border bg-card p-4">
        <div class="grid gap-3 md:grid-cols-4">
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
            <label class="mb-1 block text-xs text-muted-foreground">Type</label>
            <Select v-model="typeValue">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="All" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All</SelectItem>
                <SelectItem value="income">Income</SelectItem>
                <SelectItem value="expense">Expense</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div>
            <label class="mb-1 block text-xs text-muted-foreground"
              >Category</label
            >
            <Select v-model="categoryValue">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="All" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All</SelectItem>
                <SelectItem
                  v-for="c in props.categories"
                  :key="c.id"
                  :value="String(c.id)"
                >
                  {{ c.name }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>

        <div class="mt-4 flex flex-col gap-2 sm:flex-row">
          <Button type="button" @click="apply" class="w-full sm:w-auto"
            >Apply</Button
          >
          <Button
            type="button"
            variant="outline"
            @click="clear"
            class="w-full sm:w-auto"
            >Clear</Button
          >
          <div class="hidden flex-1 sm:block" />
          <a :href="exportUrl" download class="w-full sm:w-auto">
            <Button type="button" variant="outline" class="w-full sm:w-auto">
              <Download class="mr-2 h-4 w-4" />
              Export CSV
            </Button>
          </a>
        </div>
      </div>

      <div class="rounded-xl border bg-card p-4">
        <div
          class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
          <div class="text-sm font-medium">Results</div>
          <div
            class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center"
          >
            <div class="text-xs text-muted-foreground">
              Showing {{ props.transactions.data.length }} item(s)
            </div>
            <CreateTransaction
              :categories="props.categories"
              class="w-full sm:w-auto"
            />
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
                <th class="px-2 py-2 text-left font-medium">Date</th>
                <th class="px-2 py-2 text-left font-medium">Type</th>
                <th class="px-2 py-2 text-left font-medium">Category</th>
                <th class="px-2 py-2 text-right font-medium">Amount</th>
                <th class="px-2 py-2 text-left font-medium">Description</th>
                <th class="px-2 py-2 text-right font-medium">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              <tr
                v-for="t in props.transactions.data"
                :key="t.id"
                class="hover:bg-muted/50"
              >
                <td class="px-2 py-2 whitespace-nowrap">
                  {{ formatDate(t.transacted_at) }}
                </td>
                <td class="px-2 py-2">
                  <Badge
                    :variant="t.type === 'income' ? 'success' : 'destructive'"
                    class="capitalize"
                  >
                    {{ t.type }}
                  </Badge>
                </td>
                <td class="px-2 py-2">
                  {{ t.category?.name ?? '—' }}
                </td>
                <td
                  class="px-2 py-2 text-right font-medium tabular-nums"
                  :class="{
                    'text-success': t.type === 'income',
                    'text-destructive': t.type === 'expense',
                  }"
                >
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
