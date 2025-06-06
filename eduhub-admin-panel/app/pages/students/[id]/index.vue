<script setup lang="ts">
import * as z from "zod";
import type { FormSubmitEvent } from "@nuxt/ui";
import { defineEmits } from "vue";

const parentStore = useParentStore();
const studentStore = useStudentStore();
const emit = defineEmits(["updateStudent"]);

const props = defineProps<{
  student: object;
}>();

const fileRef = ref<HTMLInputElement>();

const profileSchema = z.object({
  name: z.string().min(2, "Too short"),
  email: z.string().email("Invalid email"),
  gender: z.string().min(2, "Too short"),
  grade_level: z.string().optional(),
  phone: z.string().optional(),
  image: z.string().optional(),
  school_name: z.string().optional(),
});

type ProfileSchema = z.output<typeof profileSchema>;

const profile = reactive<Partial<ProfileSchema>>({
  name: "",
  email: "",
  gender: "",
  grade_level: "",
  phone: "",
  image: "",
  school_name: "",
  parent_id: "",
});

onMounted(async () => {
  if (props.student) {
    parentStore.loadParentsForSelect();
    Object.assign(profile, props.student);
    profile.parent_id = {
      label: props.student?.parent?.name,
      value: props.student?.parent?.id,
    };
  }
});

const toast = useToast();

async function onSubmit(event: FormSubmitEvent<ProfileSchema>) {
  const formData = new FormData();
  // Append text fields
  formData.append("name", event.data?.name || "");
  formData.append("email", event.data?.email || "");
  formData.append("gender", event.data?.gender || "");
  formData.append("grade_level", event.data?.grade_level || "");
  formData.append("phone", event.data?.phone || "");
  formData.append("school_name", event.data?.school_name || "");
  formData.append("parent_id", event.data?.parent_id?.value || "");

  // Append the image file if exists
  if (fileRef.value?.files?.[0]) {
    formData.append("image", fileRef.value.files[0]);
  }

  studentStore.editStudent(formData, props.student?.id);

  emit("updateStudent", profile);

  toast.add({
    title: "تم تحديث المعلومات",
    description: "تم تحديث إعدادات الطالب بنجاح.",
    icon: "i-lucide-check",
    color: "success",
  });
}

function onFileChange(e: Event) {
  const input = e.target as HTMLInputElement;

  if (!input.files?.length) {
    return;
  }

  profile.image = URL.createObjectURL(input.files[0]!);
}

function onFileClick() {
  fileRef.value?.click();
}
</script>

<template>
  <UForm
    id="settings"
    :schema="profileSchema"
    :state="profile"
    @submit="onSubmit"
  >
    <UPageCard
      title="معلومات الطالب"
      :description="profile.name"
      variant="naked"
      orientation="horizontal"
      class="mb-4"
      style="font-size: 20px"
    >
      <UButton
        form="settings"
        label="حفظ التغييرات"
        color="neutral"
        type="submit"
        class="w-fit lg:ms-auto"
        style="font-size: 18px"
      />
    </UPageCard>

    <UPageCard variant="subtle">
      <UFormField
        name="name"
        label="اسم الطالب"
        description="اسم الطالب المستخدم الخاص بك."
        required
        class="flex max-sm:flex-col justify-between items-start gap-4"
        style="font-size: 18px"
      >
        <UInput
          v-model="profile.name"
          autocomplete="off"
          style="width: 300px"
        />
      </UFormField>
      <USeparator />
      <UFormField
        name="email"
        label="البريد الإلكتروني"
        description="البريد الإلكتروني الفريد الخاص بك لتسجيل الدخول ."
        required
        class="flex max-sm:flex-col justify-between items-start gap-4"
        style="font-size: 18px"
      >
        <UInput
          v-model="profile.email"
          type="email"
          autocomplete="off"
          style="width: 300px"
        />
      </UFormField>
      <USeparator />
      <UFormField
        name="phone"
        label="رقم الهاتف"
        description="رقم الهاتف الفريد الخاص بك في ملفك الشخصي."
        required
        class="flex max-sm:flex-col justify-between items-start gap-4"
        style="font-size: 18px"
      >
        <UInput
          v-model="profile.phone"
          type="tel"
          autocomplete="off"
          style="width: 300px"
        />
      </UFormField>
      <USeparator />
      <UFormField
        name="gender"
        label="الجنس"
        description="جنسك المستخدمالخاص بك في ملفك الشخصي."
        required
        class="flex max-sm:flex-col justify-between items-start gap-4"
        style="font-size: 18px"
      >
        <UInput
          v-model="profile.gender"
          autocomplete="off"
          style="width: 300px"
        />
      </UFormField>
      <USeparator />
      <UFormField
        name="grade_level"
        label="السنة الدراسية"
        description="السنة الدراسية الخاصة بك في ملفك الشخصي."
        required
        class="flex max-sm:flex-col justify-between items-start gap-4"
        style="font-size: 18px"
      >
        <UInput
          v-model="profile.grade_level"
          autocomplete="off"
          style="width: 300px"
        />
      </UFormField>
      <USeparator />
      <UFormField
        name="school_name"
        label="اسم المدرسة"
        description="اسم المدرسة الفريد الخاص بك في ملفك الشخصي."
        required
        class="flex max-sm:flex-col justify-between items-start gap-4"
        style="font-size: 18px"
      >
        <UInput
          v-model="profile.school_name"
          autocomplete="off"
          style="width: 300px"
        />
      </UFormField>
      <USeparator />
      <UFormField
        name="parent_id"
        label="ولي الأمر"
        description="اسم ولي الأمر الفريد الخاص بالطالب ."
        required
        class="flex max-sm:flex-col justify-between items-start gap-4"
        style="font-size: 18px"
      >
        <USelectMenu
          :items="parentStore.parentOptions"
          v-model="profile.parent_id"
          autocomplete="off"
          style="width: 300px"
        />
      </UFormField>
      <USeparator />
      <UFormField
        name="image"
        label="صورة الملف الشخصي"
        description="JPG, GIF or PNG. 1MB Max."
        class="flex max-sm:flex-col justify-between sm:items-center gap-4"
        style="font-size: 18px"
      >
        <div class="flex flex-wrap items-center gap-3">
          <UAvatar :src="profile.image" :alt="profile.name" size="lg" />
          <UButton
            label="اختر صورة"
            color="neutral"
            @click="onFileClick"
            style="width: 300px"
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
    </UPageCard>
  </UForm>
</template>
