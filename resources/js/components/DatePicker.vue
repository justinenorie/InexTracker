<script setup lang="ts">
import {
    DateFormatter,
    getLocalTimeZone,
    parseDate,
} from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import {
    PopoverRoot,
    PopoverTrigger,
    PopoverContent,
    PopoverPortal,
} from 'reka-ui';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { cn } from '@/lib/utils';

const props = defineProps<{
    modelValue?: string;
    placeholder?: string;
    disabled?: boolean;
    name?: string;
}>();

const emit = defineEmits(['update:modelValue']);

const df = new DateFormatter('en-US', {
    dateStyle: 'medium',
});

const dateValue = computed({
    get: () => (props.modelValue ? parseDate(props.modelValue) : undefined),
    set: (val) => emit('update:modelValue', val?.toString()),
});
</script>

<template>
    <div class="relative w-full">
        <input v-if="name" type="hidden" :name="name" :value="modelValue" />
        <PopoverRoot>
            <PopoverTrigger as-child :disabled="disabled">
                <Button
                    type="button"
                    variant="outline"
                    :class="
                        cn(
                            'h-9 w-full justify-start px-3 text-left font-normal',
                            !modelValue && 'text-muted-foreground',
                        )
                    "
                >
                    <CalendarIcon class="mr-2 h-4 w-4" />
                    {{
                        dateValue
                            ? df.format(dateValue.toDate(getLocalTimeZone()))
                            : placeholder || 'Pick a date'
                    }}
                </Button>
            </PopoverTrigger>
            <PopoverPortal>
                <PopoverContent
                    align="start"
                    :side-offset="4"
                    class="z-50 w-auto rounded-md border bg-popover p-0 text-popover-foreground shadow-md outline-none data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=closed]:zoom-out-95 data-[state=open]:animate-in data-[state=open]:fade-in-0 data-[state=open]:zoom-in-95"
                >
                    <Calendar v-model="dateValue" initial-focus />
                </PopoverContent>
            </PopoverPortal>
        </PopoverRoot>
    </div>
</template>
