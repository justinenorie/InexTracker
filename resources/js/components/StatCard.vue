<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';

type Props = {
    label: string;
    value: string | number;
    hint?: string;
};

defineProps<Props>();
</script>

<template>
    <Card class="gap-0">
        <CardHeader class="py-4">
            <CardTitle class="text-sm font-medium text-muted-foreground">
                {{ label }}
            </CardTitle>
        </CardHeader>
        <CardContent class="pt-0">
            <div class="text-2xl font-semibold tracking-tight">
                {{ value }}
            </div>
            <div v-if="hint" class="mt-1 text-xs text-muted-foreground">
                {{ hint }}
            </div>
        </CardContent>
    </Card>
</template>
