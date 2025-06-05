<script setup lang="ts">
import * as z from "zod";

const open = ref(false);

const studentStore = useStudentStore();

const schema = z.object({
  name: z.string().min(2, "Too short"),
  phone: z.string(),
  grade_level: z.string().min(2, "Too short"),
  school_name: z.string().min(2, "Too short"),
  gender: z.string().min(2, "Too short"),
  email: z.string().email("Invalid email"),
});

type Schema = z.output<typeof schema>;

const state = reactive<Partial<Schema>>({
  name: "",
  email: "",
  gender: "",
  grade_level: "",
  phone: "",
  image: "",
  school_name: "",
  parent_id: "",
});

const toast = useToast();

async function onSubmit() {
  // Add teacher to the store
  await studentStore.addStudent(state);

  // Show success toast
  toast.add({
    title: "Success",
    description: `طالب جديد ${state.name} تم اضافة بنجاح`,
    color: "success",
  });

  // Reset state after submission
  resetState();
  // Close the modal
  open.value = false;
}

function resetState() {
  Object.assign(state, {
    name: "",
    email: "",
    gender: "",
    grade_level: "",
    phone: "",
    image: "",
    school_name: "",
    parent_id: "",
  });
}

const fileRef = ref<HTMLInputElement>();

function onFileChange(e: Event) {
  const input = e.target as HTMLInputElement;

  if (!input.files?.length) {
    return;
  }

  state.image = URL.createObjectURL(input.files[0]!);
}

function onFileClick() {
  fileRef.value?.click();
}
</script>

<template>
  <UModal
    v-model:open="open"
    title="اضافة طالب"
    description="إضافة طالب جديد"
    dir="rtl"
  >
    <UButton
      label="إضافة طالب جديد"
      color="neutral"
      icon="i-lucide-plus"
      dir="rtl"
    />

    <template #body dir="rtl">
      <UForm :schema="schema" :state="state" class="space-y-4" dir="rtl">
        <UFormField
          label="اسم الطالب"
          placeholder="اسم الطالب"
          name="name"
          style="font-size: 18px"
        >
          <UInput required v-model="state.name" class="w-full" />
        </UFormField>

        <UFormField
          label="البريد الالكتروني"
          placeholder="البريد الالكتروني"
          name="email"
          style="font-size: 18px"
        >
          <UInput required v-model="state.email" class="w-full" />
        </UFormField>

        <UFormField
          label="رقم تليفون الطالب"
          placeholder="رقم تليفون الطالب"
          name="phone"
          style="font-size: 18px"
        >
          <UInput type="tel" required v-model="state.phone" class="w-full" />
        </UFormField>

        <UFormField label="نوع الطالب" placeholder="نوع الطالب" name="gender">
          <UInput required v-model="state.gender" class="w-full" />
        </UFormField>
        <UFormField
          label="اسم المدرسة"
          placeholder="اسم المدرسة"
          name="school_name"
          style="font-size: 18px"
        >
          <UInput required v-model="state.school_name" class="w-full" />
        </UFormField>
        <UFormField
          label="ولي امر الطالب"
          placeholder="ولي امر الطالب"
          name="parent_id"
          style="font-size: 18px"
        >
          <UInput required v-model="state.parent_id" class="w-full" />
        </UFormField>
        <UFormField
          label="الصف الدراسي"
          placeholder="الصف الدراسي"
          name="grade_level"
          style="font-size: 18px"
        >
          <UInput required v-model="state.grade_level" class="w-full" />
        </UFormField>
        <UFormField
          name="image"
          label="صورة الملف الشخصي"
          description="JPG, GIF or PNG. 1MB Max."
          class="flex max-sm:flex-col justify-between sm:items-center gap-4"
          style="font-size: 18px"
        >
          <div class="flex flex-wrap items-center gap-3">
            <UAvatar :src="state.image" :alt="state.name" size="lg" />
            <UButton
              label="اختر صورة"
              color="neutral"
              @click="onFileClick"
            />
            <input
              ref="fileRef"
              type="file"
              class="hidden"
              accept=".jpg, .jpeg, .png, .gif"
              @change="onFileChange"
            />
          </div>
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
