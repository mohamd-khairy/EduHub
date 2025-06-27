<script setup lang="ts">
import * as z from "zod";
import type { FormSubmitEvent } from "@nuxt/ui";

const authStore = useAuthStore();
const profile = ref({
  name: authStore.user?.name,
  email: authStore.user?.email,
  phone: authStore.user?.phone,
  avatar: authStore.user?.image,
  display_role: authStore.roles[0],
});

function onFileChange(e: Event) {
  const input = e.target as HTMLInputElement;

  if (!input.files?.length) {
    return;
  }

  profile.avatar = URL.createObjectURL(input.files[0]!);
}

function onFileClick() {
  fileRef.value?.click();
}
</script>

<template>
  <UPageCard variant="subtle">
    <img
      v-if="profile.avatar"
      class="mx-auto rounded-full object-cover"
      :src="profile.avatar"
      :alt="profile.name"
    />
    <UFormField
      name="role"
      label="الدور"
      description="الدور الخاص بك في ملفك الشخصي."
      required
      class="flex max-sm:flex-col justify-between items-start gap-4"
    >
      <UInput
        disabled
        style="width: 500px"
        v-model="profile.display_role"
        autocomplete="off"
      />
    </UFormField>
    <USeparator />

    <UFormField
      name="name"
      label="الاسم"
      description="اسمك المستخدم الخاص بك في ملفك الشخصي."
      required
      class="flex max-sm:flex-col justify-between items-start gap-4"
    >
      <UInput
        disabled
        style="width: 500px"
        v-model="profile.name"
        autocomplete="off"
      />
    </UFormField>
    <USeparator />
    <UFormField
      name="email"
      label="البرديد الالكتروني"
      description="البرديد الالكتروني الفريد الخاص بك لتسجيل الدخول."
      required
      class="flex max-sm:flex-col justify-between items-start gap-4"
    >
      <UInput
        disabled
        style="width: 500px"
        v-model="profile.email"
        type="email"
        autocomplete="off"
      />
    </UFormField>
    <USeparator />
    <UFormField
      name="phone"
      label="رقم الهاتف"
      description="رقم الهاتف الفريد الخاص بك في ملفك الشخصي."
      required
      class="flex max-sm:flex-col justify-between items-start gap-4"
    >
      <UInput
        disabled
        style="width: 500px"
        v-model="profile.phone"
        type="phone"
        autocomplete="off"
      />
    </UFormField>
  </UPageCard>
</template>
