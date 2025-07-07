<script setup lang="ts">
import { format, isToday } from "date-fns";
import type { Mail } from "~/types";
const chatStore = useChatStore();
const props = defineProps<{
  mails: Mail[];
}>();

const mailsRefs = ref<Element[]>([]);
const selectedMail = defineModel<Mail | null>();
watch(selectedMail, () => {
  if (!selectedMail.value) {
    return;
  }
  if (!selectedMail.value.last_message.is_read) {
    selectedMail.value.last_message.is_read = true;
    chatStore.markAllAsRead(selectedMail.value.id);
  }
  const ref = mailsRefs.value[selectedMail.value.id];
  if (ref) {
    ref.scrollIntoView({ block: "nearest" });
  }
});

defineShortcuts({
  arrowdown: () => {
    const index = props.mails.findIndex(
      (mail) => mail.id === selectedMail.value?.id
    );

    if (index === -1) {
      selectedMail.value = props.mails[0];
    } else if (index < props.mails.length - 1) {
      selectedMail.value = props.mails[index + 1];
    }
  },
  arrowup: () => {
    const index = props.mails.findIndex(
      (mail) => mail.id === selectedMail.value?.id
    );

    if (index === -1) {
      selectedMail.value = props.mails[props.mails.length - 1];
    } else if (index > 0) {
      selectedMail.value = props.mails[index - 1];
    }
  },
});
</script>

<template>
  <div class="overflow-y-auto divide-y divide-default">
    <div
      v-for="(mail, index) in mails"
      :key="index"
      :ref="el => { mailsRefs[mail.id] = el as Element }"
    >
      <div
        class="p-4 sm:px-6 text-sm cursor-pointer border-l-2 transition-colors"
        :class="[
          !mail.last_message.is_read ? 'text-highlighted' : 'text-toned)',
          selectedMail && selectedMail.id === mail.id
            ? 'border-primary bg-primary/10'
            : 'border-(--ui-bg) hover:border-primary hover:bg-primary/5',
        ]"
        @click="selectedMail = mail"
      >
        <div class="relative flex min-h-[3.5rem]">
          <!-- Avatar behind everything -->
          <img
            v-if="mail.other_user?.image"
            class="absolute top-0 right-0 z-0 w-13 h-12 opacity-70 object-cover rounded-full pointer-events-none"
            :src="mail.other_user?.image ?? `assets/images/chat.png`"
            :alt="mail.other_user?.name"
          />
          <img
            v-else
            class="absolute top-0 right-0 z-0 w-13 h-12 opacity-70 object-cover rounded-full pointer-events-none"
            src="assets/images/chat.png"
            :alt="mail.other_user?.name"
          />
          <!-- Text content in front -->
          <div class="pr-15 flex flex-col justify-center z-10 w-full">
            <div
              class="flex items-center justify-between"
              :class="[!mail.last_message.is_read && 'font-semibold']"
            >
              <div class="flex items-center gap-2">
                {{ mail.other_user?.name }}
                <UChip v-if="!mail.last_message.is_read" />
              </div>

              <span class="text-xs text-muted">
                {{
                  isToday(new Date(mail.created_at))
                    ? format(new Date(mail.created_at), "HH:mm")
                    : format(new Date(mail.created_at), "dd MMM")
                }}
              </span>
            </div>

            <p
              class="text-dimmed text-sm line-clamp-1"
              :class="[!mail.last_message.is_read && 'font-semibold']"
            >
              {{ mail.other_user?.phone }}
            </p>
            <p
              class="text-dimmed text-sm line-clamp-1"
              :class="[!mail.last_message.is_read && 'font-semibold']"
            >
              {{ mail.other_user?.email }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
