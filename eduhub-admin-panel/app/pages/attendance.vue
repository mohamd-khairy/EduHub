<script setup>
definePageMeta({
  permission: "read-attendance",
});
import { ref, computed, onMounted } from "vue";
import { Html5QrcodeScanner } from "html5-qrcode";

const groupStore = useGroupStore();
const attendanceStore = useAttendanceStore();
const authStore = useAuthStore();

const toast = useToast();
const now = new Date();
const dashboardTitle = ref("نظام الحضور والغياب");
const items = ref([]);
const selectedHistoryDate = ref("");
const currentDate = ref("");
const searchQuery = ref("");
const classFilter = ref(null);
const page = ref(1);
const pageCount = ref(10);
const selectedGroupTab = ref(null);
const selectedScheduleTab = ref(null);
const students = ref([]);
const paginatedStudents = ref([]);
const isScanning = ref(false);
const scanSuccess = ref(false);
const scanError = ref(false);
const scanErrorMessage = ref("");
const lastScannedStudent = ref("");
const statusOptions = ["حضر", "غائب", "متأخر"];
const statusColors = {
  حضر: "success",
  غائب: "error",
  متأخر: "warning",
  "-": "default",
};
const UButton = resolveComponent("UButton");
const UBadge = resolveComponent("UBadge");
const UDropdownMenu = resolveComponent("UDropdownMenu");

// Initialize
onMounted(async () => {
  selectedHistoryDate.value = now.toLocaleDateString("en-CA");
});

async function getAllGroups() {
  items.value = [];
  await groupStore.loadAllGroupsByTime(selectedHistoryDate.value);
  items.value = groupStore.groupsByTime;
}

watch(items, () => {
  selectedGroupTab.value = null;
  if (items.value.length > 0) {
    selectedGroupTab.value = items.value[0].value;
  }
});

watch(selectedHistoryDate, async (date) => {
  if (!date) return;

  const [year, month, day] = date.split("-");
  const dateObj = new Date(+year, +month - 1, +day);

  currentDate.value = dateObj.toLocaleDateString("ar-EG", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  });

  getAllGroups();
});

watch(selectedGroupTab, async (group_id) => {
  selectedScheduleTab.value = null;
  const group = items.value.find((item) => item.id == group_id);

  if (group?.current_schedules?.length > 0) {
    selectedScheduleTab.value = group.current_schedules[0].value;
  }
});

watch(selectedScheduleTab, async (schedule_id) => {
  loadStudentAttendance();
});

async function loadStudentAttendance() {
  students.value = [];
  groupStore.isLoading = true;
  const payload = {
    group_id: selectedGroupTab.value,
    schedule_id: selectedScheduleTab.value,
  };
  await groupStore.loadGroupTodayAttendance(payload, selectedHistoryDate.value);
  students.value = groupStore.todayGroupAttendance?.students;
  groupStore.isLoading = false;
}

// Computed Properties
const filteredStudents = computed(() => {
  let result = students.value;

  if (searchQuery.value) {
    // updatePage(1)
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (s) =>
        s.name.toLowerCase().includes(query) ||
        s.attendance_status.toLowerCase().includes(query) ||
        s.grade_level.toLowerCase().includes(query)
    );
  }

  if (classFilter.value) {
    result = result.filter((s) => s.class === classFilter.value);
  }

  return result;
});

function paginateStudents() {
  const start = (page.value - 1) * pageCount.value;
  const end = start + pageCount.value;
  paginatedStudents.value = filteredStudents.value.slice(start, end);
}

watch([students, page], () => {
  paginateStudents();
});

watch(filteredStudents, () => {
  updatePage(1);
  paginateStudents();
});

function updatePage(p) {
  page.value = p;
}

const presentCount = computed(
  () => students.value?.filter((s) => s.attendance_status === "حضر").length || 0
);
const absentCount = computed(
  () =>
    students.value?.filter((s) => s.attendance_status === "غائب").length || 0
);
const lateCount = computed(
  () =>
    students.value?.filter((s) => s.attendance_status === "متأخر").length || 0
);

const presentPercentage = computed(() => {
  const total = students.value?.length || 0;
  return total ? Math.round((presentCount.value / total) * 100) : 0;
});

const absentPercentage = computed(() => {
  const total = students.value?.length || 0;
  return total ? Math.round((absentCount.value / total) * 100) : 0;
});

const latePercentage = computed(() => {
  const total = students.value?.length || 0;
  return total ? Math.round((lateCount.value / total) * 100) : 0;
});

const startScanner = () => {
  scanSuccess.value = false;
  scanError.value = false;

  const scanner = new Html5QrcodeScanner(
    "reader", // The ID of the div to show the scanner feed
    {
      fps: 10, // Frames per second for scanning
      qrbox: 250, // Size of the scanning box
      videoConstraints: {
        facingMode: "environment", // Use the rear camera (set to "user" for front-facing)
      },
    },
    false
  );

  scanner.render(onScanSuccess, onScanError);
};

const onScanSuccess = (decodedText, decodedResult) => {
  const student = students.value.find((s) => s.id == decodedText);

  if (!student) {
    return;
  }

  if (!scanSuccess.value) {
    scanSuccess.value = true;

    markAttendance(student, "حضر");
  }
};

// This function is called when an error occurs while scanning
const onScanError = (errorMessage) => {
  scanError.value = true;
};

function getRowItems(row) {
  return [
    { type: "label", label: "الاجراءات" },
    { type: "separator" },
    {
      label: " حضر",
      icon: "i-lucide-circle-check",
      color: "primary",
      onSelect() {
        markAttendance(row.original, "حضر");
      },
    },
    {
      label: " غائب",
      icon: "i-lucide-circle-x",
      color: "error",
      onSelect() {
        markAttendance(row.original, "غائب");
      },
    },
    {
      label: " متأخر",
      icon: "i-lucide-clock",
      color: "warning",
      onSelect() {
        markAttendance(row.original, "متأخر");
      },
    },
  ];
}

// Table Columns
const columns = [
  { accessorKey: "id", id: "id", header: "الرقم الجامعي", sortable: true },
  { accessorKey: "name", id: "name", header: "اسم الطالب", sortable: true },
  {
    accessorKey: "grade_level",
    id: "grade_level",
    header: "الصف",
    sortable: true,
  },
  {
    accessorKey: "attendance_status",
    id: "attendance_status",
    header: "حالة الحضور",
    cell: ({ row }) =>
      h(
        UBadge,
        {
          class: "capitalize",
          variant: "subtle",
          color: statusColors[row.original.attendance_status],
        },
        () => row.original.attendance_status
      ),
  },
  {
    accessorKey: "actions",
    id: "actions",
    header: "إجراءات",
    cell: ({ row }) => {
      return h(
        "div",
        { class: "text-right" },
        h(
          UDropdownMenu,
          {
            content: {
              align: "end",
            },
            items: getRowItems(row),
            "aria-label": "Actions dropdown",
          },
          () =>
            h(UButton, {
              icon: "i-lucide-ellipsis-vertical",
              color: "neutral",
              variant: "ghost",
              class: "ml-auto",
              "aria-label": "Actions dropdown",
            })
        )
      );
    },
  },
];

const markAttendance = async (student, status) => {
  await attendanceStore.addAttendance({
    student_id: student?.id ?? student,
    group_id: selectedGroupTab.value,
    schedule_id: selectedScheduleTab.value,
    status,
  });
  student.attendance_status = status;
  toast.add({
    title: "نجاح",
    description: `تم تسجيل ${status} للطالب ${student.name}`,
    color: statusColors[status],
  });
};

const markAll = async (status) => {
  await attendanceStore.updateAllAttendance({
    group_id: selectedGroupTab.value,
    schedule_id: selectedScheduleTab.value,
    status,
  });
  students.value.forEach((s) => (s.attendance_status = status));
  toast.add({
    title: "نجاح",
    description: `تم تحديد جميع الطلاب كـ${status}`,
    color: statusColors[status],
  });
};

const exportAttendance = () => {
  // In a real app, this would generate and download a file
  const filters = {
    group_id: selectedGroupTab.value,
    schedule_id: selectedScheduleTab.value,
    date: selectedHistoryDate.value,
  };
  exportToExcel("attendance", filters);

  toast.add({
    title: "نجاح",
    description: `تم تصدير بيانات الحضور`,
    color: "success",
  });
};
</script>
<template>
  <UDashboardPanel id="attendance-dashboard" class="flex-grow">
    <template #header>
      <UDashboardNavbar :title="dashboardTitle">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
        <template #right>
          <div class="flex items-center gap-2">
            <UBadge color="green" variant="soft" class="text-base">
              {{ currentDate }}
            </UBadge>
            <UInput type="date" v-model="selectedHistoryDate" size="xl" />
          </div>
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <UTabs
        v-if="items.length > 0"
        v-model="selectedGroupTab"
        :items="items"
        size="xl"
      >
        <template #content="{ item, index }">
          <UTabs
            v-model="selectedScheduleTab"
            :items="item.current_schedules"
            variant="link"
            size="xl"
          >
            <template #content="{ item: schedule }">
              <main
                v-if="!groupStore.isLoading"
                class="container mx-auto p-4 grid grid-cols-1 lg:grid-cols-3 gap-6 text-base"
              >
                <!-- Main Content Area -->
                <div class="lg:col-span-2 space-y-6">
                  <!-- Manual Entry Card -->
                  <UCard class="h-full">
                    <template #header>
                      <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-semibold">
                          الإدخال اليدوي للحضور
                        </h2>
                        <div class="flex items-center gap-2">
                          <UBadge
                            color="green"
                            variant="subtle"
                            class="text-base"
                          >
                            حضر {{ presentCount }} / {{ students.length }}
                          </UBadge>
                          <UButton
                            @click="exportAttendance"
                            icon="i-heroicons-arrow-down-tray"
                            color="gray"
                            variant="ghost"
                            size="xl"
                            class="text-lg"
                            loading-auto
                            style="cursor: pointer"
                            title="تصدير بيانات الحضور"
                          />
                        </div>
                      </div>
                    </template>

                    <!-- Search and Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
                      <UInput
                        v-model="searchQuery"
                        icon="i-heroicons-magnifying-glass"
                        placeholder="ابحث بالاسم أو الرقم..."
                        size="xl"
                        class="md:col-span-1 text-lg"
                      />
                      <UButton
                        @click="markAll('حضر')"
                        color="success"
                        variant="outline"
                        icon="i-heroicons-check-circle"
                        label="تحديد الكل حضر"
                        block
                        size="md"
                        class="text-lg"
                      />
                      <UButton
                        @click="markAll('متأخر')"
                        color="warning"
                        variant="outline"
                        icon="i-heroicons-x-circle"
                        label="تحديد الكل متأخر"
                        block
                        size="md"
                        class="text-lg"
                      />
                      <UButton
                        @click="markAll('غائب')"
                        color="error"
                        variant="outline"
                        icon="i-heroicons-x-circle"
                        label="تحديد الكل غائب"
                        block
                        size="md"
                        class="text-lg"
                      />
                    </div>

                    <!-- Student Table -->
                    <div class="border rounded-lg overflow-hidden text-lg">
                      <UTable
                        :columns="columns"
                        :data="paginatedStudents"
                        :ui="{
                          th: { base: 'whitespace-nowrap bg-gray-50 text-lg' },
                          td: { base: 'max-w-[200px] text-lg' },
                          divide: 'divide-gray-200',
                        }"
                        class="w-full"
                      >
                      </UTable>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center mt-4 text-lg">
                      <div class="text-gray-500">
                        عرض {{ paginatedStudents.length }} من
                        {{ filteredStudents.length }} طالب
                      </div>

                      <UPagination
                        dir="ltr"
                        :total="filteredStudents.length"
                        :items-per-page="pageCount"
                        :default-page="page"
                        @update:page="(p) => updatePage(p)"
                      />
                    </div>
                  </UCard>
                </div>

                <!-- Summary Panel -->
                <div class="space-y-6 text-lg">
                  <!-- Attendance Stats -->
                  <UCard>
                    <template #header>
                      <h2 class="text-2xl font-semibold">إحصائيات الحضور</h2>
                    </template>

                    <div class="space-y-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                          <UIcon
                            name="i-heroicons-user-group"
                            class="w-6 h-6 text-gray-500"
                          />
                          <span>إجمالي الطلاب</span>
                        </div>
                        <span class="font-medium">{{ students.length }}</span>
                      </div>

                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                          <UIcon
                            name="i-heroicons-check-circle"
                            class="w-6 h-6 text-green-500"
                          />
                          <span>الحضور</span>
                        </div>
                        <span class="font-medium text-green-600"
                          >{{ presentCount }} ({{ presentPercentage }}%)</span
                        >
                      </div>

                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                          <UIcon
                            name="i-heroicons-x-circle"
                            class="w-6 h-6 text-red-500"
                          />
                          <span>الغياب</span>
                        </div>
                        <span class="font-medium text-red-600"
                          >{{ absentCount }} ({{ absentPercentage }}%)</span
                        >
                      </div>

                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                          <UIcon
                            name="i-heroicons-clock"
                            class="w-6 h-6 text-amber-500"
                          />
                          <span>المتأخرون</span>
                        </div>
                        <span class="font-medium text-amber-600"
                          >{{ lateCount }} ({{ latePercentage }}%)</span
                        >
                      </div>
                    </div>
                  </UCard>

                  <UCard>
                    <template #header>
                      <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-semibold">
                          مسح رمز الاستجابة السريعة
                        </h2>
                      </div>
                    </template>
                    <div class="relative">
                      <div
                        class="border-2 border-dashed border-gray-300 rounded-xl aspect-square flex items-center justify-center bg-gray-50 mb-4 dark:bg-(--chip-dark) dark:border-gray-600"
                        :class="{
                          'border-green-500': scanSuccess,
                          'border-red-500': scanError,
                        }"
                      >
                        <template v-if="!isScanning">
                          <div class="text-center p-4">
                            <UIcon
                              name="i-heroicons-qr-code"
                              class="w-20 h-20 text-gray-400 mb-4"
                            />
                            <p
                              class="text-lg text-gray-500 mb-4"
                              style="cursor: pointer"
                              @click="startScanner"
                            >
                              اضغط لبدء المسح الضوئي
                            </p>

                            <div>
                              <div
                                id="reader"
                                style="width: 300px; height: 100%"
                              ></div>
                            </div>
                          </div>
                        </template>
                      </div>
                    </div>
                  </UCard>
                </div>
              </main>

              <div
                v-else
                class="hidden lg:flex flex-col items-center justify-center flex-1 gap-4 text-center p-8"
              >
                <span
                  class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-gray-900 dark:border-gray-100"
                >
                </span>
                <p class="text-gray-700 dark:text-gray-300 text-sm">
                  جاري تحميل البيانات...
                </p>
              </div>
            </template>
          </UTabs>
        </template>
      </UTabs>
      <div v-else class="flex items-center justify-center h-64">
        لا يوجد بيانات في هذا اليوم
      </div>
    </template>
  </UDashboardPanel>
</template>

<style>
html[dir="rtl"] .rtl\:text-right {
  text-align: right;
}

html[dir="rtl"] .rtl\:text-left {
  text-align: left;
}

/* Base font size for the entire app */
html {
  font-size: 16px;
  /* A good base, you can adjust */
}

.tabs-custom .tab-active {
  border-color: #10b981;
  /* Tailwind green-500 */
  color: #059669;
  /* Tailwind green-600 */
  font-weight: 600;
}
</style>
