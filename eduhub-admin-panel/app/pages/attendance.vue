<script setup>
import { ref, computed, onMounted } from "vue";

const groupStore = useGroupStore();
const attendanceStore = useAttendanceStore();
const toast = useToast();

const currentDate = ref("");
const dashboardTitle = ref("نظام الحضور والغياب");
const items = ref([]);
const selectedHistoryDate = ref(""); // For historical attendance date picker
const searchQuery = ref("");
const classFilter = ref(null);
const page = ref(1);
const pageCount = ref(10);
const selectedGroupTab = ref(null);
const selectedScheduleTab = ref(null);
const students = ref([]);
const paginatedStudents = ref([]);
const currentMode = ref("manualEntry");
const switchMode = (mode) => {
  currentMode.value = mode;
};
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
};
const UButton = resolveComponent("UButton");
const UBadge = resolveComponent("UBadge");
const UDropdownMenu = resolveComponent("UDropdownMenu");
const UCheckbox = resolveComponent("UCheckbox");

// Initialize
onMounted(async () => {
  const now = new Date();
  currentDate.value = now.toLocaleDateString("ar-EG", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  });

  await groupStore.loadAllGroupsByTime();
  items.value = groupStore.groupsByTime;

  if (items.value.length > 0) {
    selectedGroupTab.value = items.value[0].value;
  }
});

watch(selectedGroupTab, async (group_id) => {
  const group = items.value.find((item) => item.id == group_id);
  if (group.current_schedules.length > 0) {
    selectedScheduleTab.value = group.current_schedules[0].value;
  }
});

watch(selectedScheduleTab, async (schedule_id) => {
  students.value = [];
  groupStore.isLoading = true;
  await groupStore.loadGroupTodayAttendance(
    selectedGroupTab.value,
    schedule_id
  );
  students.value = groupStore.todayGroupAttendance?.students;
  groupStore.isLoading = false;
});

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

const scannedCount = computed(
  () => students.value.filter((s) => s.status === "حضر").length
);

const startScanner = () => {
  isScanning.value = true;
  scanSuccess.value = false;
  scanError.value = false;
  // Simulate scanning
  setTimeout(() => {
    const randomStudent =
      students.value[Math.floor(Math.random() * students.value.length)];
    if (Math.random() > 0.2) {
      // 80% success rate
      randomStudent.status = "حضر";
      lastScannedStudent.value = randomStudent.name;
      scanSuccess.value = true;
      addActivity(
        `تم تسجيل حضور ${randomStudent.name}`,
        "i-heroicons-check-circle",
        "text-green-500"
      );
    } else {
      scanError.value = true;
      scanErrorMessage.value = "فشل قراءة QR - حاول مرة أخرى";
    }
    isScanning.value = false;
  }, 1500);
};

const stopScanner = () => {
  isScanning.value = false;
};

const toggleFlash = () => {
  // Flash toggle logic
  console.log("Flash toggled");
};

const uploadQRImage = () => {
  // QR image upload logic
  console.log("QR image upload triggered");
};

const classOptions = computed(() => {
  const uniqueClasses = [...new Set(students.value.map((s) => s.class))];
  return uniqueClasses.map((c) => ({ value: c, label: c }));
});

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

// Activity Log
const recentActivities = ref([
  {
    message: "تم تسجيل حضور 25 طالب",
    time: "منذ 10 دقائق",
    icon: "i-heroicons-check-circle",
    color: "text-green-500",
  },
  {
    message: "تم تصدير بيانات الحضور",
    time: "منذ ساعة",
    icon: "i-heroicons-document-arrow-down",
    color: "text-blue-500",
  },
  {
    message: "تم تحديث سجلات 3 طلاب",
    time: "منذ ساعتين",
    icon: "i-heroicons-pencil-square",
    color: "text-amber-500",
  },
]);

const addActivity = (message, icon, color) => {
  recentActivities.value.unshift({
    message,
    icon,
    color,
    time: "الآن",
  });
  if (recentActivities.value.length > 5) {
    recentActivities.value.pop();
  }
};

const markAttendance = async (student, status) => {
  await attendanceStore.addAttendance({
    student_id: student.id,
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
  console.log("Exporting attendance data:", students.value);
  addActivity(
    "تم تصدير بيانات الحضور",
    "i-heroicons-document-arrow-down",
    "text-blue-500"
  );
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
            <UButton
              @click="
                switchMode(currentMode === 'qrScan' ? 'manualEntry' : 'qrScan')
              "
              :icon="
                currentMode === 'qrScan'
                  ? 'i-heroicons-pencil-square'
                  : 'i-heroicons-qr-code'
              "
              color="primary"
              :label="
                currentMode === 'qrScan'
                  ? 'التبديل للوضع اليدوي'
                  : 'التبديل للمسح الضوئي'
              "
              class="text-base"
            />
          </div>
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <UTabs v-model="selectedGroupTab" :items="items">
        <template #content="{ item, index }">
          <UTabs v-model="selectedScheduleTab" :items="item.current_schedules">
            <template #content="{ item: schedule }">
              <main
                v-if="!groupStore.isLoading"
                class="container mx-auto p-4 grid grid-cols-1 lg:grid-cols-3 gap-6 text-base"
              >
                <!-- Main Content Area -->
                <div class="lg:col-span-2 space-y-6">
                  <!-- QR Scanner Card -->
                  <UCard v-if="currentMode === 'qrScan'" class="h-full">
                    <template #header>
                      <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-semibold">
                          مسح رمز الاستجابة السريعة
                        </h2>
                        <UBadge
                          color="green"
                          variant="subtle"
                          class="text-base"
                        >
                          {{ scannedCount }} / {{ students.length }}
                        </UBadge>
                      </div>
                    </template>

                    <!-- Scanner Area -->
                    <div class="relative">
                      <div
                        class="border-2 border-dashed border-gray-300 rounded-xl aspect-square flex items-center justify-center bg-gray-50 mb-4"
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
                            <p class="text-lg text-gray-500 mb-4">
                              اضغط لبدء المسح الضوئي
                            </p>
                            <UButton
                              @click="startScanner"
                              color="green"
                              variant="solid"
                              label="بدء المسح"
                              class="mt-4 text-lg"
                              size="xl"
                            />
                          </div>
                        </template>
                        <template v-else>
                          <div
                            class="relative w-full h-full flex items-center justify-center"
                          >
                            <div
                              class="absolute inset-0 flex items-center justify-center"
                            >
                              <div
                                class="w-64 h-64 border-4 border-green-500 rounded-lg animate-pulse"
                              ></div>
                            </div>
                            <UIcon
                              name="i-heroicons-qr-code"
                              class="w-32 h-32 text-green-500 opacity-20"
                            />
                          </div>
                        </template>
                      </div>

                      <!-- Scan Status -->
                      <UAlert
                        v-if="scanSuccess"
                        icon="i-heroicons-check-circle"
                        color="green"
                        variant="subtle"
                        :title="`تم تسجيل حضور ${lastScannedStudent}`"
                        class="mb-4 text-lg"
                      />
                      <UAlert
                        v-if="scanError"
                        icon="i-heroicons-exclamation-circle"
                        color="red"
                        variant="subtle"
                        :title="scanErrorMessage"
                        class="mb-4 text-lg"
                      />
                    </div>
                  </UCard>

                  <!-- Manual Entry Card -->
                  <UCard v-else class="h-full">
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
                            {{ presentCount }} / {{ students.length }}
                          </UBadge>
                          <UButton
                            @click="exportAttendance"
                            icon="i-heroicons-arrow-down-tray"
                            color="gray"
                            variant="ghost"
                            size="xl"
                            class="text-lg"
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
                        <span class="font-medium text-amber-600">{{
                          lateCount
                        }}</span>
                      </div>
                    </div>
                  </UCard>

                  <!-- New: Historical Attendance Log Example -->
                  <UCard>
                    <template #header>
                      <h2 class="text-2xl font-semibold">
                        سجل الحضور التاريخي
                      </h2>
                    </template>
                    <div class="space-y-4">
                      <p class="text-gray-600">
                        اختر تاريخًا لعرض سجل الحضور لذلك اليوم:
                      </p>
                      <UInput
                        type="date"
                        v-model="selectedHistoryDate"
                        size="xl"
                      />
                      <div
                        v-if="selectedHistoryDate"
                        class="mt-4 p-3 bg-gray-50 rounded-lg"
                      >
                        <p class="font-semibold text-lg">
                          سجل حضور يوم: {{ selectedHistoryDate }}
                        </p>
                        <ul class="list-disc list-inside text-gray-700 mt-2">
                          <li>أحمد محمد: حضر</li>
                          <li>سارة علي: غائب</li>
                          <li>
                            (هنا ستظهر بيانات الحضور الفعلية لهذا التاريخ)
                          </li>
                        </ul>
                      </div>
                      <div v-else class="mt-4 text-gray-500">
                        <p>الرجاء اختيار تاريخ لعرض السجل.</p>
                      </div>
                    </div>
                  </UCard>

                  <!-- Recent Activity -->
                  <UCard>
                    <template #header>
                      <h2 class="text-2xl font-semibold">النشاط الأخير</h2>
                    </template>

                    <div class="space-y-4">
                      <div
                        v-for="(activity, index) in recentActivities"
                        :key="index"
                        class="flex items-center justify-between"
                      >
                        <div class="flex items-center gap-2">
                          <UIcon
                            :name="activity.icon"
                            class="w-6 h-6 mt-0.5"
                            :class="activity.color"
                          />
                          <span>{{ activity.message }}</span>
                        </div>
                        <span class="font-medium"> {{ activity.time }}</span>
                      </div>
                    </div>
                  </UCard>
                </div>
              </main>
              <div v-else class="flex items-center justify-center h-64">
                <span
                  class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-gray-900"
                ></span>
              </div>
            </template>
          </UTabs>
        </template>
      </UTabs>
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
