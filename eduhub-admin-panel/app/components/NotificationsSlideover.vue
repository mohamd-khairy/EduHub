<script setup lang="ts">
import { formatTimeAgo } from "@vueuse/core";
import type { Notification } from "~/types";
const { $echo } = useNuxtApp();

const { isNotificationsSlideoverOpen } = useDashboard();

const notifications = ref([]);
const authStore = useAuthStore();
const notificationStore = useNotificationStore();

async function markAsRead(notification) {
  isNotificationsSlideoverOpen.value = false;
  notificationStore.markAsRead(notification.id);
}

onMounted(() => {
  // $echo.private(`message.1`).listen("NewMessage", (notification) => {
  //   console.log(notification + "notification");
  // });
});

watch(
  () => notificationStore.items?.value,
  () => {
    notifications.value = notificationStore.items;
    console.log(notifications.value);
  },
  { immediate: true, deep: true }
);
</script>

<template>
  <USlideover
    v-model:open="isNotificationsSlideoverOpen"
    title="الاشعارات"
    side="left"
  >
    <template #title>
      <div class="flex flex-row items-center justify-space-between w-full">
        <span class="text-lg font-semibold">الاشعارات</span>
        <UBadge :label="notificationStore.items?.length" class="ml-2" variant="subtle" />

        <UButton
          v-if="notifications.length"
          class="ml-2"
          color="success"
          size="sm"
          icon="i-lucide-eye-off"
          @click="notificationStore.markAllAsRead()"
          title="عرض كل الاشعارات"
        />
      </div>
    </template>

    <template #body>
      <NuxtLink
        v-for="notification in notifications"
        :key="notification.id"
        :to="`${notification?.data?.url}`"
        @click.prevent="markAsRead(notification)"
        class="px-3 py-2.5 rounded-md hover:bg-elevated/50 flex items-center gap-3 relative -mx-3 first:-mt-3 last:-mb-3"
        dir="rtl"
      >
        <UChip color="error" :show="!notification.read_at" inset>
          <UAvatar
            :src="notification?.data?.image"
            :alt="notification?.data?.title"
            size="md"
          />
        </UChip>

        <div class="text-sm flex-1">
          <p class="flex items-center justify-between">
            <span class="text-highlighted font-medium">{{
              notification?.data?.title
            }}</span>

            <time
              dir="ltr"
              :datetime="notification?.created_at"
              class="text-muted text-xs"
              v-text="formatTimeAgo(new Date(notification?.created_at))"
            />
          </p>

          <p class="text-dimmed">
            {{ notification?.data?.message }}
          </p>
        </div>
      </NuxtLink>
    </template>
  </USlideover>
</template>
