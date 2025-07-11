<script setup lang="ts">
import { format } from "date-fns";
import type { Mail } from "~/types";
const chatStore = useChatStore();
const authStore = useAuthStore();

const props = defineProps<{
  mail: Mail;
}>();

watch(
  () => props.mail, // ✅ reactive prop access
  (newMail) => {
    if (!newMail) return;
    chatStore.loadAllMessages(1, newMail.id);
  },
  { immediate: true } // optional: run on initial mount
);

const emits = defineEmits(["close"]);

const reply = ref("");
const loading = ref(false);

function onSubmit() {
  loading.value = true;

  if (!reply.value.trim()) return;

  const tempMessage = {
    id: `temp-${Date.now()}`, // temporary unique ID
    message: reply.value,
    chat_id: props.mail.id,
    sender_id: authStore.user?.id,
    sent_at: new Date().toISOString(),
    is_read: false,
  };

  // Optimistically push to message list
  chatStore.item_messages.unshift(tempMessage);

  chatStore.sendMessage({
    message: reply.value,
    chat_id: props.mail.id,
  });

  reply.value = "";
  loading.value = false;
}
</script>

<template>
  <UDashboardPanel id="inbox-2">
    <UDashboardNavbar
      :title="mail.other_user?.name"
      :description="mail.other_user?.email"
      :toggle="false"
    >
      <template #leading> </template>

      <template #right>
        <UButton
          icon="i-lucide-x"
          color="neutral"
          variant="ghost"
          class="-ms-1.5"
          @click="emits('close')"
        />
      </template>
    </UDashboardNavbar>

    <div class="flex-1 p-4 sm:p-6 overflow-y-auto flex flex-col-reverse gap-3">
      <!-- 🔁 Loading Skeleton -->

      <div
        v-if="chatStore.isLoadingMessages"
        class="hidden lg:flex flex-col items-center justify-center flex-1 gap-4 text-center p-8"
      >
        <span
          class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-gray-900 dark:border-gray-100"
        >
        </span>
        <p class="text-gray-700 dark:text-gray-300 text-sm">
          جاري تحميل الرسائل...
        </p>
      </div>

      <!-- ✅ Messages List -->

      <div
        v-else-if="chatStore.item_messages?.length > 0"
        v-for="message in [...chatStore.item_messages]"
        :key="message.id"
        class="flex"
        :class="{
          'justify-start': message.sender_id === authStore.user?.id,
          'justify-end': message.sender_id !== authStore.user?.id,
        }"
      >
        <div
          class="max-w-xs sm:max-w-md px-4 py-2 rounded-lg shadow text-sm"
          :class="{
            'bg-primary text-white dark:text-black rounded-bl-none':
              message.sender_id === authStore.user?.id,
            'bg-muted text-gray-900 dark:text-white rounded-br-none':
              message.sender_id !== authStore.user?.id,
          }"
        >
          <p class="whitespace-pre-wrap">{{ message.message }}</p>
          <div class="text-xs mt-1 text-right opacity-70">
            {{ format(new Date(message.sent_at), "HH:mm dd-MM-yyyy") }}
          </div>
        </div>
      </div>

      <!-- 💤 Empty State -->
      <div v-else>
        <p class="text-center text-muted">لا يوجد رسائل</p>
      </div>
    </div>

    <div class="pb-4 px-4 sm:px-6 shrink-0">
      <UCard
        variant="subtle"
        class="mt-auto"
        :ui="{ header: 'flex items-center gap-1.5 text-dimmed' }"
      >
        <template #header>
          <UIcon name="i-lucide-reply" class="size-5" />

          <span class="text-sm truncate">
            قم بإرسال رسالة الي ({{ mail.receiver?.email }})
            {{ mail.receiver?.name }}
          </span>
        </template>

        <form @submit.prevent="onSubmit" v-if="authStore.hasPermission('create-chatmessage')">
          <UTextarea
            v-model="reply"
            color="neutral"
            variant="none"
            required
            autoresize
            placeholder="اكتب رسالتك هنا"
            :rows="4"
            :disabled="
              loading || !authStore.hasPermission('create-chatmessage')
            "
            class="w-full"
            :ui="{ base: 'p-0 resize-none' }"
          />

          <div class="flex items-center justify-between">
            <!-- <UTooltip text="Attach file">
              <UButton
                color="neutral"
                variant="ghost"
                icon="i-lucide-paperclip"
                :disabled="
                  loading || !authStore.hasPermission('create-chatmessage')
                "
              />
            </UTooltip> -->

            <div class="flex items-center justify-end gap-2">
              <UButton
                type="submit"
                color="neutral"
                :loading="loading"
                label="ارسال"
                icon="i-lucide-send"
                style="cursor: pointer"
                :disabled="
                  loading || !authStore.hasPermission('create-chatmessage')
                "
              />
            </div>
          </div>
        </form>
      </UCard>
    </div>
  </UDashboardPanel>
</template>
