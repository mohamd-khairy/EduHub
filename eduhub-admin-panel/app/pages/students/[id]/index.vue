<script setup lang="ts">
import * as z from "zod";
import type { FormSubmitEvent } from "@nuxt/ui";

const props = defineProps<{
  student: object
}>()

const fileRef = ref<HTMLInputElement>();

const profileSchema = z.object({
  name: z.string().min(2, "Too short"),
  email: z.string().email("Invalid email"),
  gender: z.string().min(2, "Too short"),
  grade_level: z.string().optional(),
  phone: z.string().optional(),
  image: z.string().optional(),
  school_name: z.string().optional()
});

type ProfileSchema = z.output<typeof profileSchema>;

const profile = reactive<Partial<ProfileSchema>>({
  name: '',
  email: '',
  gender: '',
  grade_level: '',
  phone: '',
  image: '',
  school_name: ''
})

onMounted(async () => {
    if (props.student) {
      Object.assign(profile, props.student);
    }
});

const toast = useToast();
async function onSubmit(event: FormSubmitEvent<ProfileSchema>) {
  toast.add({
    title: "تم تحديث المعلومات",
    description: "تم تحديث إعدادات الطالب بنجاح.",
    icon: "i-lucide-check",
    color: "success",
  });
  console.log(event.data);
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
      description="جميع المعلومات الشخصيه التي تخص الطالب"
      variant="naked"
      orientation="horizontal"
      class="mb-4"
    >
      <UButton
        form="settings"
        label="حفظ التغييرات"
        color="neutral"
        type="submit"
        class="w-fit lg:ms-auto"
      />
    </UPageCard>

    <UPageCard variant="subtle">
      <UFormField
        name="name"
        label="اسم الطالب"
        description="اسم الطالب المستخدم في تسجيل الدخول واسم المستخدم الخاص بك."
        required
        class="flex max-sm:flex-col justify-between items-start gap-4"
      >
        <UInput v-model="profile.name" autocomplete="off" />
      </UFormField>
      <USeparator />
      <UFormField
        name="email"
        label="البريد الإلكتروني"
        description="البريد الإلكتروني الفريد الخاص بك لتسجيل الدخول ورابط ملفك الشخصي."
        required
        class="flex max-sm:flex-col justify-between items-start gap-4"
      >
        <UInput v-model="profile.email" type="email" autocomplete="off" />
      </UFormField>
      <USeparator />
      <UFormField
        name="phone"
        label="رقم الهاتف"
        description="رقم الهاتف الفريد الخاص بك لتسجيل الدخول ورابط ملفك الشخصي."
        required
        class="flex max-sm:flex-col justify-between items-start gap-4"
      >
        <UInput v-model="profile.phone" type="tel" autocomplete="off" />
      </UFormField>
      <USeparator />
      <UFormField
        name="gender"
        label="الجنس"
        description="جنسك المستخدم في تسجيل الدخول ورابط ملفك الشخصي."
        required
        class="flex max-sm:flex-col justify-between items-start gap-4"
      >
        <UInput v-model="profile.gender" autocomplete="off" />
      </UFormField>
      <USeparator />
      <UFormField
        name="grade_level"
        label="السنة الدراسية"
        description="السنة الدراسية الخاصة بك لتسجيل الدخول ورابط ملفك الشخصي."
        required
        class="flex max-sm:flex-col justify-between items-start gap-4"
      >
        <UInput v-model="profile.grade_level" autocomplete="off" />
      </UFormField>
      <USeparator />
      <UFormField
        name="school_name"
        label="اسم المدرسة"
        description="اسم المدرسة الفريد الخاص بك لتسجيل الدخول ورابط ملفك الشخصي."
        required
        class="flex max-sm:flex-col justify-between items-start gap-4"
      >
        <UInput v-model="profile.school_name" autocomplete="off" />
      </UFormField>
      <USeparator />
      <UFormField
        name="image"
        label="صورة الملف الشخصي"
        description="JPG, GIF or PNG. 1MB Max."
        class="flex max-sm:flex-col justify-between sm:items-center gap-4"
      >
        <div class="flex flex-wrap items-center gap-3">
          <UAvatar :src="profile.image" :alt="profile.name" size="lg" />
          <UButton label="اختر صورة" color="neutral" @click="onFileClick" />
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
