<script setup lang="ts">
// import { Area, AreaChart, CartesianGrid, XAxis, YAxis } from "recharts"
import { VisArea, VisAxis, VisLine, VisXYContainer } from '@unovis/vue'
import { computed } from 'vue'

import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import type { ChartConfig } from '@/components/ui/chart'
import {
  ChartContainer,
  ChartCrosshair,
  ChartTooltip,
  ChartTooltipContent,
  componentToString,
} from '@/components/ui/chart'

const props = defineProps<{
  data: {
    period: string
    label: string
    income: number
    expense: number
  }[]
}>()

const chartData = computed(() => props.data)

type Data = (typeof props.data)[number]

const chartConfig = {
  income: {
    label: 'Income',
    color: 'var(--success)',
  },
  expense: {
    label: 'Expense',
    color: 'var(--destructive)',
  },
} satisfies ChartConfig

const svgDefs = `
  <linearGradient id="fillIncome" x1="0" y1="0" x2="0" y2="1">
    <stop
      offset="5%"
      stop-color="var(--color-income)"
      stop-opacity="0.8"
    />
    <stop
      offset="95%"
      stop-color="var(--color-income)"
      stop-opacity="0.1"
    />
  </linearGradient>
  <linearGradient id="fillExpense" x1="0" y1="0" x2="0" y2="1">
    <stop
      offset="5%"
      stop-color="var(--color-expense)"
      stop-opacity="0.8"
    />
    <stop
      offset="95%"
      stop-color="var(--color-expense)"
      stop-opacity="0.1"
    />
  </linearGradient>
`
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Income & Expenses</CardTitle>
      <CardDescription>
        Historical overview of your financial activity
      </CardDescription>
    </CardHeader>
    <CardContent>
      <ChartContainer :config="chartConfig" class="max-h-[350px]">
        <VisXYContainer :data="chartData" :svg-defs="svgDefs">
          <VisLine
            :x="(d: Data, i: number) => i"
            :y="[(d: Data) => d.income, (d: Data) => d.expense]"
            :color="
              (d: Data, i: number) =>
                [chartConfig.income.color, chartConfig.expense.color][i]
            "
            :line-width="1"
          />
          <VisArea
            :x="(d: Data, i: number) => i"
            :y="(d: Data) => d.income"
            color="url(#fillIncome)"
            :opacity="0.4"
          />
          <VisArea
            :x="(d: Data, i: number) => i"
            :y="(d: Data) => d.expense"
            color="url(#fillExpense)"
            :opacity="0.4"
          />
          <VisAxis
            type="x"
            :x="(d: Data, i: number) => i"
            :tick-line="false"
            :domain-line="false"
            :grid-line="false"
            :num-ticks="chartData.length"
            :tick-format="
              (d: number, index: number) => {
                return chartData[index]?.label || ''
              }
            "
          />
          <VisAxis
            type="y"
            :num-ticks="3"
            :tick-line="false"
            :domain-line="false"
            :tick-format="(d: number, index: number) => ''"
          />
          <ChartTooltip />
          <ChartCrosshair
            :template="
              componentToString(chartConfig, ChartTooltipContent, {
                labelKey: 'label',
              })
            "
            :color="
              (d: Data, i: number) =>
                [
                  chartConfig.income.color,
                  chartConfig.expense.color,
                  chartConfig.income.color,
                  chartConfig.expense.color,
                ][i]
            "
          />
        </VisXYContainer>
      </ChartContainer>
    </CardContent>
    <CardFooter>
      <div class="flex w-full items-start gap-2 text-sm">
        <div class="grid gap-2">
          <div
            class="flex items-center gap-2 leading-none text-muted-foreground"
          >
            Showing data for the selected range
          </div>
        </div>
      </div>
    </CardFooter>
  </Card>
</template>
