<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import TransactionController from '@/actions/App/Http/Controllers/TransactionController';
import InputError from '@/components/InputError.vue';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Category = {
    id: string;
    name: string;
    type: string;
};

type Transaction = {
    id: string;
    type: 'income' | 'expense' | string;
    category_id?: string | null;
    amount: string;
    transacted_at: string;
    description?: string | null;
    category?: Category | null;
};

type Props = {
    transaction: Transaction;
    categories: Category[];
};

const props = defineProps<Props>();

const isOpen = ref(false);

const typeValue = ref<'income' | 'expense'>(
    (props.transaction.type as any) === 'income' ? 'income' : 'expense',
);
const categoryIdValue = ref<string>(
    String(
        props.transaction.category_id ?? props.transaction.category?.id ?? '',
    ),
);
const amountValue = ref<string>(props.transaction.amount ?? '');
const transactedAtValue = ref<string>(props.transaction.transacted_at ?? '');
const descriptionValue = ref<string>(props.transaction.description ?? '');

watch(
    () => props.transaction,
    (t) => {
        typeValue.value = (t.type as any) === 'income' ? 'income' : 'expense';
        categoryIdValue.value = String(t.category_id ?? t.category?.id ?? '');
        amountValue.value = t.amount ?? '';
        transactedAtValue.value = t.transacted_at ?? '';
        descriptionValue.value = t.description ?? '';
    },
);

const onSuccess = () => {
    isOpen.value = false;
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button type="button" variant="outline" size="sm">Edit</Button>
        </DialogTrigger>

        <DialogContent class="sm:max-w-[560px]">
            <DialogHeader>
                <DialogTitle>Edit transaction</DialogTitle>
                <DialogDescription> Update this record. </DialogDescription>
            </DialogHeader>

            <Form
                :action="
                    TransactionController.update({
                        transaction: props.transaction.id as any,
                    }).url
                "
                method="put"
                :options="{ preserveScroll: true }"
                @success="onSuccess"
                v-slot="{ errors, processing }"
                class="space-y-4"
            >
                <div class="grid gap-2">
                    <Label>Type</Label>
                    <select
                        v-model="typeValue"
                        name="type"
                        class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        :disabled="processing"
                        required
                    >
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                    <InputError :message="errors.type" />
                </div>

                <div class="grid gap-2">
                    <Label>Category</Label>
                    <select
                        v-model="categoryIdValue"
                        name="category_id"
                        class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        :disabled="processing"
                        required
                    >
                        <option value="" disabled>Select a category</option>
                        <option
                            v-for="c in categories"
                            :key="c.id"
                            :value="String(c.id)"
                        >
                            {{ c.name }}
                        </option>
                    </select>
                    <InputError :message="errors.category_id" />
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label htmlFor="amount">Amount</Label>
                        <Input
                            id="amount"
                            v-model="amountValue"
                            name="amount"
                            inputmode="decimal"
                            placeholder="0.00"
                            :disabled="processing"
                            required
                        />
                        <InputError :message="errors.amount" />
                    </div>

                    <div class="grid gap-2">
                        <Label htmlFor="transacted_at">Date</Label>
                        <Input
                            id="transacted_at"
                            v-model="transactedAtValue"
                            name="transacted_at"
                            type="date"
                            :disabled="processing"
                            required
                        />
                        <InputError :message="errors.transacted_at" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label htmlFor="description">Description (optional)</Label>
                    <textarea
                        id="description"
                        v-model="descriptionValue"
                        name="description"
                        rows="3"
                        :disabled="processing"
                        class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        placeholder="Notes..."
                    />
                    <InputError :message="errors.description" />
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button
                            type="button"
                            variant="outline"
                            :disabled="processing"
                        >
                            Cancel
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="processing"
                        >Save changes</Button
                    >
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
