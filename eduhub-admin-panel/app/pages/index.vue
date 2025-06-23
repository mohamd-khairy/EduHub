<script setup lang="ts">
import DashboardHeader from "~/components/reports/DashboardHeader.vue";
const { isNotificationsSlideoverOpen } = useDashboard();
const resetSignal = ref(false);
const authStore = useAuthStore();
const dashboardStore = useDashboardStore();

const range = shallowRef({
  start: null,
  end: null,
});

const group_id = ref(null);
const student_id = ref(null);
const stats = ref([]);
const isLoading = ref(false);
const hasPermission = ref(false);

onMounted(async () => {
  await getDashboardReports();
  // Check permissions
    hasPermission.value = authStore.hasPermission("read-dashboard");
});

async function getDashboardReports(params = {}) {
  isLoading.value = true; // ⏳ بداية التحميل

  try {
    await Promise.all([
       dashboardStore.fetchDashboardData(params),
    ]);

   stats.value = dashboardStore.items;
    // You can also set other dashboard data here if needed
  } finally {
    isLoading.value = false; 
  }
}

watch(
  [range, group_id, student_id],
  async ([newRange, newGroupId, newStudentId]) => {
    const params = {
      start: newRange.start?.toISOString() || "",
      end: newRange.end?.toISOString() || "",
      group_id: newGroupId || "",
      student_id: newStudentId || "",
    };
    await getDashboardReports(params);
  }
);

function resetFilters() {
  resetSignal.value = !resetSignal.value; // toggle to always trigger
  range.value.start = null;
  range.value.end = null;
  group_id.value = null;
  student_id.value = null;
}
const hasFilter = computed(() => {
  return range.value.start || range.value.end || group_id.value || student_id.value;
});
</script>

<template>
  <UDashboardPanel id="home">
    <template #header>
      <DashboardHeader
        title="الصفحة الرئيسية"
        :reset-signal="resetSignal"
        :range="range"
        :group_id="group_id"
        :student_id="student_id"
        :is-loading="isLoading"
        :has-filter="hasFilter"
        @update:range="(val) => (range = val)"
        @update:group_id="(val) => (group_id = val)"
        @update:student_id="(val) => (student_id = val)"
        @reset="resetFilters"
      />
    </template>

    <template #body>
      <div
        v-if="isLoading"
        class="hidden lg:flex flex-col items-center justify-center flex-1 gap-4 text-center p-8"
      >
        <span
          class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-gray-900 dark:border-gray-100"
        ></span>
        <p class="text-gray-700 dark:text-gray-300 text-sm">جاري تحميل البيانات...</p>
      </div>
      <div
        v-else
        class="space-y-6"
      >
        <HomeStats :period="period" :range="range" :stats="stats" v-if="hasPermission" />
        <HomeChart :period="period" :range="range" v-if="hasPermission" />
        <HomeSales :period="period" :range="range" v-if="hasPermission" />
      </div>
    </template>
  </UDashboardPanel>
</template>
