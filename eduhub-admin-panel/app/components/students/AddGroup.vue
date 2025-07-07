<script setup lang="ts">
import * as z from "zod";
import { defineEmits } from "vue";

const emit = defineEmits(["updateStudent"]);

const props = defineProps<{
  student: object;
}>();

const open = ref(false);

const studentStore = useStudentStore();
const groupStore = useGroupStore();

onMounted(async () => {
  if (props.student) {
    state.student_id = props.student?.id
    state.status = true
  }
  await groupStore.loadGroupsForSelect();
});

const schema = z.object({});

type Schema = z.output<typeof schema>;

const state = reactive<Partial<Schema>>({
  group_id: "",
  student_id: "",
  start_date: "",
  end_date: "",
  status: "",
});

const toast = useToast();

async function onSubmit() {
  state.group_id = state.group_id.value
  // Add teacher to the store
  await studentStore.addEnrollment(state);

  emit("updateStudent", state);

  // Show success toast
  toast.add({
    title: "تم بنجاح",
    description: `مجموعة جديدة ${state.name} تم اضافة بنجاح`,
    color: "success",
  });

  // Reset state after submission
  resetState();
  // Close the modal
  open.value = false;
}

function resetState() {
  Object.assign(state, {
    group_id: "",
    start_date: "",
    end_date: "",
  });
}
</script>

<template>
  <UModal v-model:open="open" title="اضافة مجموعة" description="إضافة مجموعة جديدة" dir="rtl">
    <UButton label="إضافة مجموعة جديدة" color="neutral" icon="i-lucide-plus" dir="rtl" class="w-fit lg:ms-auto"
      style="font-size: 18px" />

    <template #body dir="rtl">
      <UForm :schema="schema" :state="state" class="space-y-4" dir="rtl">
        <UFormField label="اسم المجموعة" placeholder="اسم المجموعة" name="group_id" style="font-size: 18px">
          <USelectMenu required :items="groupStore.groupOptions" v-model="state.group_id" class="w-full" />
        </UFormField>
        <UFormField label="تاريخ البداية" placeholder="تاريخ البداية" name="start_date" style="font-size: 18px">
          <UInput required type="date" v-model="state.start_date" class="w-full" />
        </UFormField>

        <UFormField label="تاريخ النهاية" placeholder="تاريخ النهاية" name="end_date" style="font-size: 18px">
          <UInput required type="date" v-model="state.end_date" class="w-full" />
        </UFormField>
        <div class="flex justify-end gap-2">
          <UButton label="الغاء" color="neutral" variant="subtle" @click="open = false" />
          <UButton label="حفظ" color="primary" variant="solid" type="submit" loading-auto @click="onSubmit" />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
