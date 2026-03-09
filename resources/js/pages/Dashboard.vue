<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3'
import { TrendingDown, TrendingUp, Wallet } from 'lucide-vue-next'
import { computed } from 'vue'
import Chart from '@/components/Chart.vue'
import DateRangeBar from '@/components/DateRangeBar.vue'
import Heading from '@/components/Heading.vue'
import PieChart from '@/components/PieChart.vue'
import StatCard from '@/components/StatCard.vue'
import { useCurrency } from '@/composables/useCurrency'
import AppLayout from '@/layouts/AppLayout.vue'
import CreateTransaction from '@/pages/transactions/CreateTransaction.vue'
import { dashboard } from '@/routes'
import type {
  BreadcrumbItem,
  Category,
  DashboardHistoryItem,
  DashboardTotals,
  TotalsByCategoryRow,
} from '@/types'

type Props = {
  filters: {
    from?: string | null
    to?: string | null
  }
  totals: DashboardTotals
  totalsByCategory: TotalsByCategoryRow[]
  history: DashboardHistoryItem[]
  categories: Category[]
}

const props = defineProps<Props>()

const page = usePage()
const user = computed(() => page.props.auth.user as any)
const { formatMoney } = useCurrency()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard(),
  },
]

const firstName = computed(() => {
  return user.value.name ? user.value.name.split(' ')[0] : 'User'
})
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
      <div
        class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
      >
        <Heading
          :title="`Welcome back, ${firstName}!`"
          description="Here's an overview of your finances. Use the date range to focus your reporting."
        />
        <CreateTransaction
          :categories="props.categories"
          class="w-full sm:w-auto"
        />
      </div>

      <DateRangeBar
        :from="props.filters.from ?? null"
        :to="props.filters.to ?? null"
        :submit-url="({ from, to }) => dashboard.url({ query: { from, to } })"
      />

      <div class="grid gap-6 md:grid-cols-3">
        <StatCard
          label="Current Balance"
          :value="formatMoney(user.balance)"
          :icon="Wallet"
        />
        <StatCard
          label="Total Income"
          :value="formatMoney(props.totals.total_income)"
          variant="success"
          :icon="TrendingUp"
        />
        <StatCard
          label="Total Expenses"
          :value="formatMoney(props.totals.total_expense)"
          variant="destructive"
          :icon="TrendingDown"
        />
      </div>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
        <Chart class="lg:col-span-3" :data="props.history" />
        <PieChart class="lg:col-span-2" :data="props.totalsByCategory" />
      </div>
    </div>
  </AppLayout>
</template>
