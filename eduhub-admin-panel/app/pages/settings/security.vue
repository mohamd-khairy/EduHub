<script setup lang="ts">
import * as z from "zod";
import type { FormError } from "@nuxt/ui";

const authStore = useAuthStore();

const passwordSchema = z.object({
  current: z.string().min(8, "كلمة المرور لا تقل عن 8 حروف"),
  new: z.string().min(8, "كلمة المرور لا تقل عن 8 حروف"),
});

type PasswordSchema = z.output<typeof passwordSchema>;

const password = reactive<Partial<PasswordSchema>>({
  current: undefined,
  new: undefined,
});

const validate = (state: Partial<PasswordSchema>): FormError[] => {
  const errors: FormError[] = [];
  if (state.current && state.new && state.current === state.new) {
    errors.push({
      name: "new",
      message: " يجب ان تكون كلمة المرور الجديدة مختلفة عن كلمة المرور الحالية",
    });
  }
  return errors;
};

function ChangePassword() {
  const payload = {
    current_password: password.current,
    new_password: password.new,
    type: authStore.roles[0],
    id: authStore.user?.id,
  };
  authStore.changePassword(payload);
}
</script>

<template>
  <UPageCard
    title="كلمة المرور"
    description="تغيير كلمة المرور الخاصة بك. يجب ان تكون كلمة المرور الجديدة مختلفة عن كلمة المرور الحالية."
    variant="subtle"
  >
    <UForm
      :schema="passwordSchema"
      :state="password"
      :validate="validate"
      class="flex flex-col gap-4 max-w-xs"
      @submit="ChangePassword"
    >
      <UFormField name="current">
        <UInput
          v-model="password.current"
          type="password"
          placeholder="كلمة المرور الحالية"
          class="w-full"
        />
      </UFormField>

      <UFormField name="new">
        <UInput
          v-model="password.new"
          type="password"
          placeholder="كلمة المرور الجديدة"
          class="w-full"
        />
      </UFormField>

      <UButton label="تغيير كلمة المرور" class="w-fit" type="submit" />
    </UForm>
  </UPageCard>

  <UPageCard
    title="حذف الحساب"
    description="حذف الحساب الخاص بك. هذا لا يمكن التراجع عنه. سيتم حذف جميع المعلومات الخاصة بك."
    class="bg-gradient-to-tl from-error/10 from-5% to-default"
  >
    <template #footer>
      <UButton label="حذف الحساب" disabled color="error" />
    </template>
  </UPageCard>
</template>
