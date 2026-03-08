<script setup lang="ts">
import { Donut } from '@unovis/ts';
import { VisDonut, VisSingleContainer } from '@unovis/vue';
import { computed, ref } from 'vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    ChartContainer,
    ChartTooltip,
    ChartTooltipContent,
    componentToString,
} from '@/components/ui/chart';
import type { ChartConfig } from '@/components/ui/chart';

type TotalsByCategoryRow = {
    category_id: string;
    category_name: string;
    category_color: string;
    type: string;
    total: string;
};

const props = defineProps<{
    data: TotalsByCategoryRow[];
}>();

const activeType = ref<'income' | 'expense'>('expense');

const filteredData = computed(() => {
    return props.data.filter(row => row.type === activeType.value);
});

const chartData = computed(() => {
    return filteredData.value.map((row) => ({
        category: row.category_name,
        amount: Math.abs(Number(row.total)),
        color: row.category_color || '#ccc',
        // Add a dynamic key so ChartTooltipContent finds it in chartConfig
        [row.category_name]: Math.abs(Number(row.total)),
    }));
});

type Data = (typeof chartData.value)[number];

const chartConfig = computed(() => {
    const config: ChartConfig = {};

    filteredData.value.forEach((row) => {
        config[row.category_name] = {
            label: row.category_name,
            color: row.category_color,
        };
    });

    return config;
});
</script>

<template>
    <Card class="flex flex-col">
        <CardHeader class="pb-2">
            <div class="flex items-center justify-between">
                <div>
                    <CardTitle class="text-base font-medium">Breakdown</CardTitle>
                    <CardDescription>By category color</CardDescription>
                </div>
                <div class="flex rounded-md border p-0.5 text-xs">
                    <button
                        type="button"
                        @click="activeType = 'expense'"
                        :class="[
                            'px-2 py-1 rounded-sm transition-colors',
                            activeType === 'expense' ? 'bg-destructive text-white' : 'hover:bg-muted'
                        ]"
                    >
                        Expense
                    </button>
                    <button
                        type="button"
                        @click="activeType = 'income'"
                        :class="[
                            'px-2 py-1 rounded-sm transition-colors',
                            activeType === 'income' ? 'bg-success text-white' : 'hover:bg-muted'
                        ]"
                    >
                        Income
                    </button>
                </div>
            </div>
        </CardHeader>
        <CardContent class="flex-1 pb-0">
            <div v-if="chartData.length === 0" class="flex h-[200px] items-center justify-center text-sm text-muted-foreground italic">
                No {{ activeType }} categories found.
            </div>
            <ChartContainer
                v-else
                :config="chartConfig"
                class="mx-auto aspect-square max-h-[250px]"
            >
                <VisSingleContainer
                    :data="chartData"
                    :margin="{ top: 20, bottom: 20, left: 20, right: 20 }"
                >
                    <VisDonut
                        :value="(d: Data) => d.amount"
                        :color="(d: Data) => d.color"
                        :arc-width="30"
                    />
                    <ChartTooltip
                        :triggers="{
                            [Donut.selectors.segment]: componentToString(
                                chartConfig,
                                ChartTooltipContent,
                                { hideLabel: true },
                            )!,
                        }"
                    />
                </VisSingleContainer>
            </ChartContainer>
        </CardContent>
        <CardFooter class="pt-0">
            <div class="text-xs text-center w-full text-muted-foreground">
                Showing the distribution of your <strong>{{ activeType }}s</strong> across different categories for the selected period.
            </div>
        </CardFooter>
    </Card>
</template>
