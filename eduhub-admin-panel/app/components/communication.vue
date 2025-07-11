<script setup lang="ts">
const authStore = useAuthStore();
const notificationStore = useNotificationStore();
const { isNotificationsSlideoverOpen } = useDashboard();

onMounted(async () => {
  await notificationStore.loadAllNotifications();
});
</script>

<template v-if="hasPermission">
  <UTooltip
    text="Notifications"
    :shortcuts="['N']"
    class="mr-2"
    v-if="authStore.hasPermission('read-notification')"
  >
    <UButton
      color="neutral"
      variant="ghost"
      square
      @click="isNotificationsSlideoverOpen = true"
      style="cursor: pointer"
    >
      <UChip color="error" inset v-if="notificationStore.items?.length > 0">
        <UIcon name="i-lucide-bell" class="size-5 shrink-0" />
      </UChip>
      <UIcon v-else name="i-lucide-bell" class="size-5 shrink-0" />
    </UButton>
  </UTooltip>

  <RouterLink to="/inbox" v-if="authStore.hasPermission('read-chat')">
    <UButton
      icon="i-lucide-send"
      size="md"
      color="neutral"
      variant="ghost"
      style="cursor: pointer"
    />
  </RouterLink>
</template>
