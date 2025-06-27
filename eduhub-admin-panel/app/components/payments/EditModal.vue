<script setup lang="ts">
import * as z from "zod";

const paymentStore = usePaymentStore();
const studentStore = useStudentStore();

const props = defineProps({
  open: Boolean,
  item: Object,
});

const emit = defineEmits(["update:open"]);

watch(
  () => props.open,
  (val) => {
    open.value = val;
  }
);

const open = ref(false);
const schema = z.object({});
const toast = useToast();

watch(open, (val) => {
  emit("update:open", val);
});

type Schema = z.output<typeof schema>;

async function onSubmit() {
  const payload = {
    amount: state.amount,
    payment_date: state.payment_date,
    method: state.method,
    status: state.status,
    note: state.note,
    student_id: state.student_id?.value,
  };

  paymentStore.editPayment(payload, props.item?.id);

  toast.add({
    title: "تم بنجاح",
    description: `تم تعديل بنجاح`,
    color: "success",
  });

  open.value = false;
}

const state = reactive<Partial<Schema>>({
  student_id: null,
  amount: null,
  payment_date: null,
  method: null,
  status: null,
  note: null,
});

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

watch(
  () => props.item,
  (val) => {
    if (!val) return;
    state.payment_date = val.payment_date || "";
    state.amount = val.amount || null;
    state.method = val.method || null;
    state.status = val.status || null;
    state.note = val.note || null;
    state.student_id = val.student?.name || null;
  },
  { immediate: true, deep: true }
);
</script>

<template>
  <UModal
    v-model:open="open"
    title="تعديل كورس"
    description="تعديل كورس "
    dir="rtl"
  >
    <template #body dir="rtl">
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
