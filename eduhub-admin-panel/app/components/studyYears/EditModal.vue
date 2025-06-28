<script setup lang="ts">
import * as z from "zod";
const studyYearStore = useStudyYearStore();

const schema = z.object({
  name: z.string().min(2, "Too short"),
});

type Schema = z.output<typeof schema>;

const toast = useToast();

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

watch(open, (val) => {
  emit("update:open", val);
});

async function onSubmit() {
  studyYearStore.editStudyYear(state, props.item?.id);
  toast.add({
    title: "تم بنجاح",
    description: `سنة دراسية  ${state.name} تم تعديل بنجاح`,
    color: "success",
  });
  open.value = false;
}

const state = reactive<Partial<Schema>>({
  name: null,
  start_date: null,
  end_date: null,
  status: null,
});

watch(
  () => props.item,
  (val) => {
    if (!val) return;
    state.name = val.name || "";
    state.start_date = val.start_date || null;
    state.end_date = val.end_date || null;
    state.status = val.status || null;
  },
  { immediate: true, deep: true }
);
</script>

<template>
  <UModal
    v-model:open="open"
    title="تعديل سنة دراسية"
    description="تعديل سنة دراسية "
    dir="rtl"
  >
    <template #body dir="rtl">
      <UForm :schema="schema" :state="state" class="space-y-4" dir="rtl">
        <UFormField
          label="اسم السنة الدراسية"
          placeholder="اسم السنة الدراسية"
          name="name"
        >
          <UInput required v-model="state.name" class="w-full" />
        </UFormField>

        <UFormField
          label="تاريخ بدء السنة الدراسية"
          placeholder="تاريخ بدء السنة الدراسية"
          name="start_date"
        >
          <UInput
            type="date"
            required
            v-model="state.start_date"
            class="w-full"
          />
        </UFormField>

        <UFormField
          label="تاريخ نهاية السنة الدراسية"
          placeholder="تاريخ نهاية السنة الدراسية"
          name="end_date"
        >
          <UInput
            type="date"
            required
            v-model="state.end_date"
            class="w-full"
          />
        </UFormField>

        <UFormField
          label="حالة السنة الدراسية"
          placeholder="حالة السنة الدراسية"
          name="status"
        >
          <USwitch required v-model="state.status" class="w-full" />
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
            loading-auto
            type="submit"
            @click="onSubmit"
          />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
