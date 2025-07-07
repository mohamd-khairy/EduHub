<script setup lang="ts">
const props = defineProps<{
  title: string;
  resetSignal: boolean;
  range: any;
  group_id: any;
  student_id: any;
  isLoading: boolean;
  hasFilter: boolean;
}>();

const emit = defineEmits<{
  (e: "update:range", value: any): void;
  (e: "update:group_id", value: any): void;
  (e: "update:student_id", value: any): void;
  (e: "reset"): void;
}>();

const { isNotificationsSlideoverOpen } = useDashboard();
const authStore = useAuthStore();
const notificationStore = useNotificationStore();

onMounted(async () => {
  await notificationStore.loadAllNotifications();
});

const items = [
  [
    {
      label: "رسالة جديدة",
      icon: "i-lucide-send",
      to: "/inbox",
    },
  ],
];
</script>

<template>
  <UDashboardNavbar :title="title" :ui="{ right: 'gap-3' }">
    <template #leading>
      <UDashboardSidebarCollapse />
    </template>

    <template #right v-if="authStore.hasPermission('read-notification')">
      <UTooltip text="Notifications" :shortcuts="['N']">
        <UButton
          color="neutral"
          variant="ghost"
          square
          @click="isNotificationsSlideoverOpen = true"
        >
          <UChip color="error" inset v-if="notificationStore.items?.length > 0">
            <UIcon name="i-lucide-bell" class="size-5 shrink-0" />
          </UChip>
          <UIcon v-else name="i-lucide-bell" class="size-5 shrink-0" />
        </UButton>
      </UTooltip>

      <RouterLink to="/inbox">
        <UButton
          icon="i-lucide-send"
          size="md"
          color="neutral"
          variant="ghost"
        />
      </RouterLink>
    </template>
  </UDashboardNavbar>

  <UDashboardToolbar>
    <template #left>
      <HomeDateRangePicker
        :reset-signal="resetSignal"
        :model-value="range"
        @update:model-value="(val) => emit('update:range', val)"
        class="-ms-1"
        :disabled="isLoading"
      />

      <HomeStudentSelect
        :model-value="student_id"
        @update:model-value="(val) => emit('update:student_id', val)"
        :range="range"
        :disabled="isLoading"
      />

      <HomeGroupSelect
        :model-value="group_id"
        @update:model-value="(val) => emit('update:group_id', val)"
        :range="range"
        :disabled="isLoading"
      />

      <UButton
        v-if="hasFilter"
        icon="i-lucide-x"
        color="gray"
        size="sm"
        @click="emit('reset')"
        class="hover:bg-gray-200"
      />
    </template>
  </UDashboardToolbar>
</template>
