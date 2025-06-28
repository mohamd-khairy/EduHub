<script setup lang="ts">
import * as z from "zod";

const open = ref(false);
const studyYearStore = useStudyYearStore();
const authStore = useAuthStore();

const schema = z.object({
  name: z.string().min(2, "Too short"),
});

type Schema = z.output<typeof schema>;

const state = reactive<Partial<Schema>>({
  name: null,
  start_date: null,
  end_date: null,
  status: null,
});

const toast = useToast();
async function onSubmit() {
  studyYearStore.addStudyYear(state);
  toast.add({
    title: "تم بنجاح",
    description: `سنة دراسية جديد ${state.name} تم اضافة بنجاح`,
    color: "success",
  });
  open.value = false;
  resetState();
}
function resetState() {
  Object.assign(state, {
    name: null,
    start_date: null,
    end_date: null,
    status: null,
  });
}
</script>

<template>
  <UModal
    v-model:open="open"
    title="اضافة سنة دراسية"
    description="إضافة سنة دراسية جديد"
    dir="rtl"
  >
    <UButton
      label="إضافة سنة دراسية جديد"
      icon="i-lucide-plus"
      dir="rtl"
      v-if="authStore.hasPermission('create-studyyear')"
    />

    <template #body dir="rtl">
      <UForm dir="rtl" :schema="schema" :state="state" class="space-y-4">
        <UFormField label="اسم السنة دراسية" placeholder="اسم السنة دراسية" name="name">
          <UInput required v-model="state.name" class="w-full" />
        </UFormField>

        <UFormField label="تاريخ بدء السنة دراسية" placeholder="تاريخ بدء السنة دراسية" name="start_date">
          <UInput type="date" required v-model="state.start_date" class="w-full" />
        </UFormField>

        <UFormField label="تاريخ نهاية السنة دراسية" placeholder="تاريخ نهاية السنة دراسية" name="end_date">
          <UInput type="date" required v-model="state.end_date" class="w-full" />
        </UFormField>

        <UFormField label="حالة السنة دراسية" placeholder="حالة السنة دراسية" name="status">
          <USwitch  required v-model="state.status" class="w-full" />
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
