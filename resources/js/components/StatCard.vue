<script setup lang="ts">
import type { Component } from 'vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';

type Props = {
    label: string;
    value: string | number;
    hint?: string;
    variant?: 'default' | 'success' | 'destructive' | 'warning';
    icon?: Component;
};

defineProps<Props>();
</script>

<template>
    <Card
        class="gap-0 transition-all hover:shadow-md"
        :class="{
            'bg-primary/5 border-primary/20': !variant || variant === 'default',
            'bg-success/5 border-success/20': variant === 'success',
            'bg-destructive/5 border-destructive/20': variant === 'destructive',
            'bg-warning/5 border-warning/20': variant === 'warning',
        }"
    >
        <CardHeader class="flex flex-row items-center justify-between space-y-0 py-4 pb-2">
            <CardTitle
                class="text-sm font-medium"
                :class="{
                    'text-primary/80': !variant || variant === 'default',
                    'text-success/80': variant === 'success',
                    'text-destructive/80': variant === 'destructive',
                    'text-warning/80': variant === 'warning',
                    'text-muted-foreground': !variant || variant === 'default'
                }"
            >
                {{ label }}
            </CardTitle>
            <component
                :is="icon"
                v-if="icon"
                class="h-4 w-4"
                :class="{
                    'text-primary/80': !variant || variant === 'default',
                    'text-success/80': variant === 'success',
                    'text-destructive/80': variant === 'destructive',
                    'text-warning/80': variant === 'warning',
                }"
            />
        </CardHeader>
        <CardContent class="pt-0">
            <div
                class="text-2xl font-bold tracking-tight"
                :class="{
                    'text-primary': !variant || variant === 'default',
                    'text-success': variant === 'success',
                    'text-destructive': variant === 'destructive',
                    'text-warning': variant === 'warning',
                }"
            >
                {{ value }}
            </div>
            <div v-if="hint" class="mt-1 text-xs text-muted-foreground">
                {{ hint }}
            </div>
        </CardContent>
    </Card>
</template>
