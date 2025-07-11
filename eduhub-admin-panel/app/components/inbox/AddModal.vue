<script setup lang="ts">
import * as z from "zod";

const open = ref(false);
const chatStore = useChatStore();
const authStore = useAuthStore();
const search = ref("");
const allUsers = ref([]);
const schema = z.object({});

type Schema = z.output<typeof schema>;

const state = reactive<Partial<Schema>>({
  user: null,
});

const toast = useToast();
async function onSubmit() {
  const user = {
    id: state.user?.value,
    type: state.user?.type,
  };
  chatStore.createChat(user);
  toast.add({
    title: "تم بنجاح",
    description: `تم اضافة بنجاح`,
    color: "success",
  });
  open.value = false;
  resetState();
}
function resetState() {
  Object.assign(state, {
    user: null,
  });
}

watch(search, async () => {
  await chatStore.loadAllUsers(search.value);
  allUsers.value = chatStore.all_users;
});
</script>

<template>
  <UModal
    v-model:open="open"
    title="اضافة محادثة"
    description="إضافة محادثة جديد"
    dir="rtl"
  >
    <UButton
      label="إضافة محادثة جديد"
      icon="i-lucide-plus"
      dir="rtl"
      v-if="authStore.hasPermission('create-chat')"
    />

    <template #body dir="rtl">
      <UForm dir="rtl" :schema="schema" :state="state" class="space-y-4">
        <UFormField label="اسم المحادثة" placeholder="اسم المحادثة" name="name">
          <USelectMenu
            required
            dir="rtl"
            v-model:search-term="search"
            v-model="state.user"
            :items="allUsers"
            class="w-full"
            placeholder="اختر المستخدم"
            :search-input="{
              placeholder: 'بحث...',
              icon: 'i-lucide-search',
            }"
          />
        </UFormField>

        <div class="flex justify-end gap-2">
          <UButton
            label="الغاء"
            color="neutral"
            variant="subtle"
            @click="open = false"
          />
          <UButton
            label="بدء المحادثة"
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
