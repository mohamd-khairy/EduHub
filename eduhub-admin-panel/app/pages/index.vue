<script setup lang="ts">
import { sub } from "date-fns";
import type { Period, Range } from "~/types";
const { isNotificationsSlideoverOpen } = useDashboard();
const resetSignal = ref(false);
const authStore = useAuthStore();
const dashboardStore = useDashboardStore();

const range = shallowRef<Range>({
  start: null,
  end: null,
});

const group_id = ref(null);
const stats = ref([]);

const hasPermission = ref(false);
onMounted(async () => {
  await dashboardStore.fetchDashboardData();
  stats.value = dashboardStore.items;

  if (authStore.hasPermission("read-dashboard")) {
    hasPermission.value = true;
  }
});

watch([range, group_id], async ([newRange, newGroupId]) => {
  await dashboardStore.fetchDashboardData({
    start: newRange.start?.toISOString() || "",
    end: newRange.end?.toISOString() || "",
    group_id: newGroupId || "",
  });
  stats.value = dashboardStore.items;
});

function resetFilters() {
  resetSignal.value = !resetSignal.value; // toggle to always trigger
  range.value.start = null;
  range.value.end = null;
  group_id.value = null;
}
const hasFilter = computed(() => {
  return range.value.start || range.value.end || group_id.value;
});
</script>

<template>
  <UDashboardPanel id="home">
    <template #header>
      <UDashboardNavbar title="الصفحة الرئيسية" :ui="{ right: 'gap-3' }">
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
              <UChip color="error" inset>
                <UIcon name="i-lucide-bell" class="size-5 shrink-0" />
              </UChip>
            </UButton>
          </UTooltip>
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <template #left>
          <HomeDateRangePicker
            :reset-signal="resetSignal"
            v-model="range"
            class="-ms-1"
          />

          <HomeGroupSelect v-model="group_id" :range="range" />

          <UButton
            v-if="hasFilter"
            icon="i-lucide-x"
            color="gray"
            size="sm"
            @click="resetFilters()"
            class="hover:bg-gray-200"
          />
        </template>
      </UDashboardToolbar>
    </template>

    <template #body>
      <HomeStats
        :period="period"
        :range="range"
        :stats="stats"
        v-if="hasPermission"
      />
      <HomeChart :period="period" :range="range" v-if="hasPermission" />
      <HomeSales :period="period" :range="range" v-if="hasPermission" />
    </template>
  </UDashboardPanel>
</template>
