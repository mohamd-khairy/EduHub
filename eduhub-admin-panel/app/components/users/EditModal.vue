<script setup lang="ts">
import * as z from "zod";
import { defineEmits } from "vue";
import type { FormSubmitEvent } from "@nuxt/ui";

const userStore = useUserStore();
const roleStore = useRoleStore();

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

const toast = useToast();
type Schema = z.output<typeof schema>;

const state = reactive<Partial<Schema>>({
  name: "",
  email: "",
  password: "",
  roles: {},
  phone: "",
  image: "",
});

const schema = z.object({
  name: z.string().min(2, "Too short"),
  email: z.string().email("Invalid email"),
  phone: z.string(),
});

async function onSubmit(event: FormSubmitEvent<Schema>) {
  const formData = new FormData();

  // Append text fields with fallback values
  formData.append("name", event.data?.name ?? "");
  formData.append("email", event.data?.email ?? "");
  formData.append("password", event.data?.password ?? "");
  formData.append("phone", event.data?.phone ?? "");
  formData.append(`roles[0]`, event.data?.roles?.value);

  // Append image file if exists
  const file = fileRef.value?.files?.[0];
  if (file) {
    formData.append("image", file);
  }

  try {
    await userStore.editUser(formData, props.item?.id);

    emit("update:open", false);

    // Success feedback
    toast.add({
      title: "تعديل الموظف بنجاح.",
      description: `تمت تعديل ${state.name} بنجاح.`,
      color: "success",
    });

    resetState();
    open.value = false;
  } catch (error) {
    console.error("Submission failed:", error);
    toast.add({
      title: "خطأ",
      description: "حدث خطأ أثناء إضافة الموظف.",
      color: "error",
    });
  }
}

function resetState() {
  Object.assign(state, {
    name: "",
    email: "",
    password: "",
    roles: {},
    phone: "",
    image: "",
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

watch(
  () => props.item,
  (val) => {
    if (!val) return;

    if (val) {
      state.name = val.name;
      state.email = val.email;
      state.password = val.password;
      state.phone = val.phone;
      state.image = val.image;
      state.roles = val.roles.map((role) => {
        return { value: role.id, label: role.name };
      })[0];
    }
  }
);
</script>

<template>
  <UModal
    v-model:open="open"
    title="تعديل موظف"
    description="تعديل موظف جديد"
    dir="rtl"
  >
    <template #body dir="rtl">
      <UForm
        :schema="schema"
        :state="state"
        class="space-y-4"
        dir="rtl"
        @submit="onSubmit"
      >
        <UFormField
          label="اسم الموظف"
          placeholder="اسم الموظف"
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
          <UInput required v-model="state.email" class="w-full" type="email" />
        </UFormField>

        <UFormField
          label="كلمة المرور"
          placeholder="كلمة المرور"
          name="password"
          style="font-size: 18px"
        >
          <UInput v-model="state.password" class="w-full" type="password" />
        </UFormField>

        <UFormField
          label="رقم تليفون الموظف"
          placeholder="رقم تليفون الموظف"
          name="phone"
          style="font-size: 18px"
        >
          <UInput type="tel" required v-model="state.phone" class="w-full" />
        </UFormField>

        <UFormField
          label="الصلاحيات"
          placeholder="الصلاحيات"
          name="roles"
          style="font-size: 18px"
        >
          <USelectMenu
            :items="roleStore.roleOptions"
            v-model="state.roles"
            autocomplete="off"
            class="w-full"
          />
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
          />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
