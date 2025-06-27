<script setup lang="ts">
import * as z from "zod";
import type { FormSubmitEvent } from "@nuxt/ui";

const paymentStore = usePaymentStore();
const studentStore = useStudentStore();
const authStore = useAuthStore();

const toast = useToast();
const schema = z.object({});
const open = ref(false);

type Schema = z.output<typeof schema>;

const state = reactive<Partial<Schema>>({
  student_id: null,
  amount: null,
  payment_date: null,
  method: null,
  status: null,
  note: null,
});

async function onSubmit() {
  const payload = {
    amount: state.amount,
    payment_date: state.payment_date,
    method: state.method,
    status: state.status,
    note: state.note,
    student_id: state.student_id?.value,
  };

  paymentStore.addPayment(payload);

  toast.add({
    title: "تم بنجاح",
    description: `دفع جديد تم بنجاح`,
    color: "success",
  });

  open.value = false;

  resetState();
}

function resetState() {
  Object.assign(state, {
    student_id: null,
    amount: null,
    payment_date: null,
    method: null,
    status: null,
    note: null,
  });
}

const methods = ["كاش", "تحويل بنكي", "فيزا"];
const statuses = [
  { label: "تم الدفع", value: "paid" },
  { label: "في انتظار الدفع", value: "pending" },
  { label: "لم يتم الدفع", value: "cancelled" },
];

const searchStudentTerm = ref("");

watch(searchStudentTerm, (newVal) => {
  if (newVal.length >= 3 || newVal.length < 1)
    // For example, debounce here if needed to avoid too many requests
    studentStore.loadStudentsForSelect(newVal);
});

</script>

<template>
  <UModal v-model:open="open" title="اضافة دفع" description="إضافة دفع جديد">
    <UButton label="إضافة دفع جديد" icon="i-lucide-plus" v-if="authStore.hasPermission('create-parentmodel')" />

    <template #body>
      <UForm :schema="schema" :state="state" class="space-y-4" dir="rtl">
        <UFormField
          label="اسم الطالب"
          placeholder="اختر الطالب"
          name="student_id"
        >
          <USelectMenu
            required
            dir="rtl"
            v-model:search-term="searchStudentTerm"
            v-model="state.student_id"
            :items="studentStore.studentOptions"
            class="w-full"
            placeholder="اختر الطالب"
            :search-input="{
              placeholder: 'بحث...',
              icon: 'i-lucide-search',
            }"
          />
        </UFormField>

        <UFormField
          label="تاريخ الدفع"
          placeholder="تاريخ الدفع"
          name="payment_date"
        >
          <UInput
            required
            v-model="state.payment_date"
            type="date"
            placeholder="اختر التاريخ"
            class="w-full"
          />
        </UFormField>

        <UFormField label="قيمة الدفع " placeholder="قيمة الدفع " name="amount">
          <UInput
            required
            v-model="state.amount"
            max="100"
            type="number"
            placeholder="قيمة الدفع "
            class="w-full"
          />
        </UFormField>
        <UFormField
          class="w-full"
          label=" حالة الدفع"
          placeholder=" حالة الدفع"
        >
          <USelect
            required
            v-model="state.status"
            :items="statuses"
            placeholder=" حالة الدفع"
            class="w-full"
          />
        </UFormField>
        <UFormField
          class="w-full"
          label=" طريقة الدفع"
          placeholder=" طريقة الدفع"
        >
          <USelect
            required
            v-model="state.method"
            :items="methods"
            placeholder=" طريقة الدفع"
            class="w-full"
          />
        </UFormField>

        <UFormField label="الملاحظات " placeholder="الملاحظات " name="note">
          <UTextarea required v-model="state.note" class="w-full" />
        </UFormField>

        <div class="flex justify-end gap-2">
          <UButton
            label="الغاء"
            color="neutral"
            variant="subtle"
            @click="open = false"
          />
          <UButton
            label="حفظ"
            color="primary"
            variant="solid"
            type="submit"
            loading-auto
            @click="onSubmit"
          />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
