<template>
    <UDashboardPanel id="attendance-dashboard" class="flex-grow">
        <template #header>
            <UDashboardNavbar :title="dashboardTitle">
                <template #leading>
                    <UDashboardSidebarCollapse />
                </template>
                <template #trailing>
                    <div class="flex items-center gap-2">
                        <UBadge color="green" variant="soft" class="text-base">
                            {{ currentDate }}
                        </UBadge>
                        <UButton @click="switchMode(currentMode === 'qrScan' ? 'manualEntry' : 'qrScan')"
                            :icon="currentMode === 'qrScan' ? 'i-heroicons-pencil-square' : 'i-heroicons-qr-code'"
                            color="gray" variant="ghost"
                            :label="currentMode === 'qrScan' ? 'التبديل للوضع اليدوي' : 'التبديل للمسح الضوئي'"
                            class="text-base" />
                    </div>
                </template>
            </UDashboardNavbar>
        </template>

        <template #body>
            <main class="container mx-auto p-4 grid grid-cols-1 lg:grid-cols-3 gap-6 text-base">
                <!-- Main Content Area -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- QR Scanner Card -->
                    <UCard v-if="currentMode === 'qrScan'" class="h-full">
                        <template #header>
                            <div class="flex items-center justify-between">
                                <h2 class="text-2xl font-semibold">مسح رمز الاستجابة السريعة</h2>
                                <UBadge color="green" variant="subtle" class="text-base">
                                    {{ scannedCount }} / {{ students.length }}
                                </UBadge>
                            </div>
                        </template>

                        <!-- Scanner Area -->
                        <div class="relative">
                            <div class="border-2 border-dashed border-gray-300 rounded-xl aspect-square flex items-center justify-center bg-gray-50 mb-4"
                                :class="{ 'border-green-500': scanSuccess, 'border-red-500': scanError }">
                                <template v-if="!isScanning">
                                    <div class="text-center p-4">
                                        <UIcon name="i-heroicons-qr-code" class="w-20 h-20 text-gray-400 mb-4" />
                                        <p class="text-lg text-gray-500 mb-4">اضغط لبدء المسح الضوئي</p>
                                        <UButton @click="startScanner" color="green" variant="solid" label="بدء المسح"
                                            class="mt-4 text-lg" size="xl" />
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="relative w-full h-full flex items-center justify-center">
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="w-64 h-64 border-4 border-green-500 rounded-lg animate-pulse">
                                            </div>
                                        </div>
                                        <UIcon name="i-heroicons-qr-code" class="w-32 h-32 text-green-500 opacity-20" />
                                    </div>
                                </template>
                            </div>

                            <!-- Scan Status -->
                            <UAlert v-if="scanSuccess" icon="i-heroicons-check-circle" color="green" variant="subtle"
                                :title="`تم تسجيل حضور ${lastScannedStudent}`" class="mb-4 text-lg" />
                            <UAlert v-if="scanError" icon="i-heroicons-exclamation-circle" color="red" variant="subtle"
                                :title="scanErrorMessage" class="mb-4 text-lg" />
                        </div>
                    </UCard>

                    <!-- Manual Entry Card -->
                    <UCard v-else class="h-full">
                        <template #header>
                            <div class="flex items-center justify-between">
                                <h2 class="text-2xl font-semibold">الإدخال اليدوي للحضور</h2>
                                <div class="flex items-center gap-2">
                                    <UBadge color="green" variant="subtle" class="text-base">
                                        {{ presentCount }} / {{ students.length }}
                                    </UBadge>
                                    <UButton @click="exportAttendance" icon="i-heroicons-arrow-down-tray" color="gray"
                                        variant="ghost" size="xl" class="text-lg" />
                                </div>
                            </div>
                        </template>

                        <!-- Search and Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                            <UInput v-model="searchQuery" icon="i-heroicons-magnifying-glass"
                                placeholder="ابحث بالاسم أو الرقم..." size="xl" class="md:col-span-2 text-lg" />
                            <USelect v-model="classFilter" :items="classOptions" placeholder="جميع الصفوف" size="xl"
                                class="text-lg" />
                        </div>

                        <!-- Student Table -->
                        <div class="border rounded-lg overflow-hidden text-lg">
                            <UTable :columns="columns" :data="paginatedStudents" :ui="{
                                th: { base: 'whitespace-nowrap bg-gray-50 text-lg' },
                                td: { base: 'max-w-[200px] truncate text-lg' },
                                divide: 'divide-gray-200'
                            }" class="w-full">
                            </UTable>
                        </div>

                        <!-- Pagination -->
                        <div class="flex justify-between items-center mt-4 text-lg">
                            <div class="text-gray-500">
                                عرض {{ paginatedStudents.length }} من {{ filteredStudents.length }} طالب
                            </div>
                            <UPagination v-model="page" :page-count="pageCount" :total="filteredStudents.length"
                                class="text-lg" />
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
                                    <UIcon name="i-heroicons-user-group" class="w-6 h-6 text-gray-500" />
                                    <span>إجمالي الطلاب</span>
                                </div>
                                <span class="font-medium">{{ students.length }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <UIcon name="i-heroicons-check-circle" class="w-6 h-6 text-green-500" />
                                    <span>الحضور</span>
                                </div>
                                <span class="font-medium text-green-600">{{ presentCount }} ({{ presentPercentage
                                    }}%)</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <UIcon name="i-heroicons-x-circle" class="w-6 h-6 text-red-500" />
                                    <span>الغياب</span>
                                </div>
                                <span class="font-medium text-red-600">{{ absentCount }} ({{ absentPercentage
                                    }}%)</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <UIcon name="i-heroicons-clock" class="w-6 h-6 text-amber-500" />
                                    <span>المتأخرون</span>
                                </div>
                                <span class="font-medium text-amber-600">{{ lateCount }}</span>
                            </div>
                        </div>
                    </UCard>

                    <!-- Quick Actions -->
                    <UCard>
                        <template #header>
                            <h2 class="text-2xl font-semibold">إجراءات سريعة</h2>
                        </template>

                        <div class="space-y-3">
                            <UButton @click="markAllPresent" icon="i-heroicons-check-circle" color="green"
                                variant="solid" label="تحديد الكل حاضر" block size="xl" class="text-lg" />

                            <UButton @click="markAllAbsent" icon="i-heroicons-x-circle" color="red" variant="outline"
                                label="تحديد الكل غائب" block size="xl" class="text-lg" />

                            <UButton @click="exportAttendance" icon="i-heroicons-document-arrow-down" color="gray"
                                variant="outline" label="تصدير البيانات" block size="xl" class="text-lg" />
                        </div>
                    </UCard>

                    <!-- Recent Activity -->
                    <UCard>
                        <template #header>
                            <h2 class="text-2xl font-semibold">النشاط الأخير</h2>
                        </template>

                        <div class="space-y-4">
                            <div v-for="(activity, index) in recentActivities" :key="index"
                                class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <UIcon :name="activity.icon" class="w-6 h-6 mt-0.5" :class="activity.color" />
                                </div>
                                <div>
                                    <p class="font-medium">{{ activity.message }}</p>
                                    <p class="text-gray-500">{{ activity.time }}</p>
                                </div>
                            </div>
                        </div>
                    </UCard>
                </div>
            </main>
        </template>
    </UDashboardPanel>
</template>

<style>
/* Custom RTL styles */
[dir="rtl"] .rtl\:text-right {
    text-align: right;
}

[dir="rtl"] .rtl\:text-left {
    text-align: left;
}

/* Increase base font size */
html {
    font-size: 17px;
}

/* Larger badge text */
.badge-text {
    font-size: 0.8rem;
}

/* Larger table text */
.table-text {
    font-size: 1rem;
}

/* Larger button text */
.button-text {
    font-size: 1rem;
}

/* Larger input text */
.input-text {
    font-size: 1rem;
}
</style>
<script setup>
import { ref, computed, onMounted } from 'vue';

// Mode Management
const currentMode = ref('qrScan');
const switchMode = (mode) => {
    currentMode.value = mode;
};

// Date Handling
const currentDate = ref('');
const dashboardTitle = ref('نظام الحضور والغياب');

// QR Scanner State
const isScanning = ref(false);
const scanSuccess = ref(false);
const scanError = ref(false);
const scanErrorMessage = ref('');
const lastScannedStudent = ref('');
const scannedCount = computed(() => students.value.filter(s => s.status === 'حاضر').length);

const startScanner = () => {
    isScanning.value = true;
    scanSuccess.value = false;
    scanError.value = false;
    // Simulate scanning
    setTimeout(() => {
        const randomStudent = students.value[Math.floor(Math.random() * students.value.length)];
        if (Math.random() > 0.2) { // 80% success rate
            randomStudent.status = 'حاضر';
            lastScannedStudent.value = randomStudent.name;
            scanSuccess.value = true;
            addActivity(`تم تسجيل حضور ${randomStudent.name}`, 'i-heroicons-check-circle', 'text-green-500');
        } else {
            scanError.value = true;
            scanErrorMessage.value = 'فشل قراءة QR - حاول مرة أخرى';
        }
        isScanning.value = false;
    }, 1500);
};

const stopScanner = () => {
    isScanning.value = false;
};

const toggleFlash = () => {
    // Flash toggle logic
    console.log('Flash toggled');
};

const uploadQRImage = () => {
    // QR image upload logic
    console.log('QR image upload triggered');
};

// Manual Entry State
const searchQuery = ref('');
const classFilter = ref(null);
const page = ref(1);
const pageCount = ref(10);

const statusOptions = ['حاضر', 'غائب', 'متأخر'];
const statusColors = {
    'حاضر': 'success',
    'غائب': 'error',
    'متأخر': 'warning'
};

const classOptions = computed(() => {
    const uniqueClasses = [...new Set(students.value.map(s => s.class))];
    return uniqueClasses.map(c => ({ value: c, label: c }));
});

// Student Data
const students = ref([
    { id: 'S001', name: 'أحمد محمد', class: 'الصف التاسع', status: 'حاضر' },
    { id: 'S002', name: 'سارة علي', class: 'الصف التاسع', status: 'غائب' },
    { id: 'S003', name: 'خالد حسن', class: 'الصف العاشر', status: 'متأخر' },
    { id: 'S004', name: 'فاطمة إبراهيم', class: 'الصف التاسع', status: 'حاضر' },
    { id: 'S005', name: 'عمر عبدالله', class: 'الصف الحادي عشر', status: 'غائب' },
    { id: 'S006', name: 'نورا سعيد', class: 'الصف العاشر', status: 'حاضر' },
    { id: 'S007', name: 'يوسف كمال', class: 'الصف التاسع', status: 'حاضر' },
    { id: 'S008', name: 'لمى راشد', class: 'الصف الحادي عشر', status: 'غائب' },
    { id: 'S009', name: 'زياد وائل', class: 'الصف العاشر', status: 'متأخر' },
    { id: 'S010', name: 'هناء سمير', class: 'الصف التاسع', status: 'حاضر' },
    { id: 'S011', name: 'محمود خالد', class: 'الصف العاشر', status: 'حاضر' },
    { id: 'S012', name: 'ريماس فارس', class: 'الصف الحادي عشر', status: 'غائب' },
    { id: 'S013', name: 'وسام نادر', class: 'الصف التاسع', status: 'متأخر' },
    { id: 'S014', name: 'جنى سامي', class: 'الصف العاشر', status: 'حاضر' },
    { id: 'S015', name: 'باسل وليد', class: 'الصف الحادي عشر', status: 'غائب' },
]);

// Computed Properties
const filteredStudents = computed(() => {
    let result = students.value;

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(s =>
            s.name.toLowerCase().includes(query) ||
            s.id.toLowerCase().includes(query)
        );
    }

    if (classFilter.value) {
        result = result.filter(s => s.class === classFilter.value);
    }

    return result;
});

const paginatedStudents = computed(() => {
    const start = (page.value - 1) * pageCount.value;
    const end = start + pageCount.value;
    return filteredStudents.value.slice(start, end);
});

const presentCount = computed(() => students.value.filter(s => s.status === 'حاضر').length);
const absentCount = computed(() => students.value.filter(s => s.status === 'غائب').length);
const lateCount = computed(() => students.value.filter(s => s.status === 'متأخر').length);

const presentPercentage = computed(() => Math.round((presentCount.value / students.value.length) * 100));
const absentPercentage = computed(() => Math.round((absentCount.value / students.value.length) * 100));

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UCheckbox = resolveComponent('UCheckbox')

function getRowItems(row) {
    return [
        { type: 'label', label: 'الاجراءات' },
        { type: 'separator' },
        {
            label: ' حاضر',
            icon: 'i-lucide-circle-check',
            color: 'primary',
            onSelect() {
                markPresent(row)
            }
        },
        {
            label: ' غائب',
            icon: 'i-lucide-circle-x',
            color: 'error',
            onSelect() {
                markAbsent(row)
            }
        },
        {
            label: ' متأخر',
            icon: 'i-lucide-clock',
            color: 'warning',
            onSelect() {
                markLate(row)
            }
        }
    ]
}

// Table Columns
const columns = [
    { accessorKey: 'id', id: 'id', header: 'الرقم الجامعي', sortable: true },
    { accessorKey: 'name', id: 'name', header: 'اسم الطالب', sortable: true },
    { accessorKey: 'class', id: 'class', header: 'الصف', sortable: true },
    { accessorKey: 'status', id: 'status', header: 'حالة الحضور' ,
    cell: ({ row }) =>
      h(
        UBadge,
        { class: 'capitalize', variant: 'subtle', color: statusColors[row.original.status] },
        () => (row.original.status)
      )
    },
    {
        accessorKey: 'actions', id: 'actions', header: 'إجراءات',
        cell: ({ row }) => {
            return h(
                'div',
                { class: 'text-right' },
                h(
                    UDropdownMenu,
                    {
                        content: {
                            align: 'end'
                        },
                        items: getRowItems(row),
                        'aria-label': 'Actions dropdown'
                    },
                    () =>
                        h(UButton, {
                            icon: 'i-lucide-ellipsis-vertical',
                            color: 'neutral',
                            variant: 'ghost',
                            class: 'ml-auto',
                            'aria-label': 'Actions dropdown'
                        })
                )
            )
        }
    }
];

// Activity Log
const recentActivities = ref([
    { message: 'تم تسجيل حضور 25 طالب', time: 'منذ 10 دقائق', icon: 'i-heroicons-check-circle', color: 'text-green-500' },
    { message: 'تم تصدير بيانات الحضور', time: 'منذ ساعة', icon: 'i-heroicons-document-arrow-down', color: 'text-blue-500' },
    { message: 'تم تحديث سجلات 3 طلاب', time: 'منذ ساعتين', icon: 'i-heroicons-pencil-square', color: 'text-amber-500' },
]);

const addActivity = (message, icon, color) => {
    recentActivities.value.unshift({
        message,
        icon,
        color,
        time: 'الآن'
    });
    if (recentActivities.value.length > 5) {
        recentActivities.value.pop();
    }
};

// Student Actions
const markPresent = (student) => {
    student.status = 'حاضر';
    addActivity(
        `تم تسجيل حضور ${student.name}`,
        'i-heroicons-check-circle',
        'text-green-500'
    );
};

const markAbsent = (student) => {
    student.status = 'غائب';
    addActivity(
        `تم تسجيل غياب ${student.name}`,
        'i-heroicons-x-circle',
        'text-red-500'
    );
};

const markLate = (student) => {
    student.status = 'متأخر';
    addActivity(
        `تم تسجيل تأخر ${student.name}`,
        'i-heroicons-clock',
        'text-amber-500'
    );
};

const markAllPresent = () => {
    students.value.forEach(s => s.status = 'حاضر');
    addActivity('تم تحديد جميع الطلاب كحاضرين', 'i-heroicons-check-circle', 'text-green-500');
};

const markAllAbsent = () => {
    students.value.forEach(s => s.status = 'غائب');
    addActivity('تم تحديد جميع الطلاب كغائبين', 'i-heroicons-x-circle', 'text-red-500');
};

const exportAttendance = () => {
    // In a real app, this would generate and download a file
    console.log('Exporting attendance data:', students.value);
    addActivity('تم تصدير بيانات الحضور', 'i-heroicons-document-arrow-down', 'text-blue-500');
};

// Initialize
onMounted(() => {
    const now = new Date();
    currentDate.value = now.toLocaleDateString('ar-EG', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
});
</script>
