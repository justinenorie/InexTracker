<script setup lang="ts">
import { Trash } from 'lucide-vue-next';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

interface Props {
    title?: string;
    description?: string;
    confirmText?: string;
    variant?: 'destructive' | 'default';
    disabled?: boolean;
}

withDefaults(defineProps<Props>(), {
    title: 'Are you sure?',
    description: 'This action cannot be undone.',
    confirmText: 'Delete',
    variant: 'destructive',
    disabled: false,
});

const emit = defineEmits<{
    confirm: [];
}>();

const isOpen = ref(false);

const handleConfirm = () => {
    emit('confirm');
    isOpen.value = false;
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child>
            <slot name="trigger">
                <Button :variant="variant" size="sm" :disabled="disabled">
                    <Trash class="mr-2 h-4 w-4" />
                    {{ confirmText }}
                </Button>
            </slot>
        </DialogTrigger>

        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription>
                    {{ description }}
                </DialogDescription>
            </DialogHeader>

            <DialogFooter class="mt-4 gap-2">
                <DialogClose as-child>
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="disabled"
                    >
                        Cancel
                    </Button>
                </DialogClose>
                <Button
                    type="button"
                    :variant="variant"
                    :disabled="disabled"
                    @click="handleConfirm"
                >
                    {{ confirmText }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
