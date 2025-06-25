<script setup lang="ts">
const props = withDefaults(
  defineProps<{
    exam?: object;
  }>(),
  {
    exam: null,
  }
);

const examResultStore = useExamResultStore();
const examStore = useExamStore();
const studentStore = useStudentStore();

const open = ref(false);
const exam_id = ref(null);
const student_id = ref(null);
const searchExamTerm = ref("");
const searchStudentTerm = ref("");
const UButton = resolveComponent("UButton");
const UBadge = resolveComponent("UBadge");

// Columns for the table
const columns: TableColumn<User>[] = [
  {
    accessorKey: "id",
    id: "رقم الدرجة",
    header: ({ column }) => {
      const isSorted = column.getIsSorted();
      return h(UButton, {
        color: "neutral",
        variant: "ghost",
        label: "رقم الدرجة",
        icon: isSorted
          ? isSorted === "asc"
            ? "i-lucide-arrow-up-narrow-wide"
            : "i-lucide-arrow-down-wide-narrow"
          : "i-lucide-arrow-up-down",
        class: "-mx-2.5",
        onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
      });
    },
  },
  {
    accessorKey: "exam",
    id: "اسم الاختبار",
    cell: ({ row }) => h(() => row.original.exam?.title),
    header: ({ column }) => {
      const isSorted = column.getIsSorted();
      return h(UButton, {
        color: "neutral",
        variant: "ghost",
        label: "اسم الاختبار",
        icon: isSorted
          ? isSorted === "asc"
            ? "i-lucide-arrow-up-narrow-wide"
            : "i-lucide-arrow-down-wide-narrow"
          : "i-lucide-arrow-up-down",
        class: "-mx-2.5",
        onClick: () => column.toggleSorting(column.getIsSorted() === "asc"),
      });
    },
  },
  {
    accessorKey: "student",
    id: "اسم الطالب",
    header: "اسم الطالب",
    cell: ({ row }) => h(() => row.original.student?.name),
  },
  {
    accessorKey: "total_marks",
    id: "درجة الامتحان",
    header: "درجة الامتحان",
    cell: ({ row }) =>
      h(
        UBadge,
        { class: "capitalize", variant: "subtle", color: "warning" },
        () => row.original.exam?.total_marks
      ),
  },
  {
    accessorKey: "score",
    id: "درجة الطالب",
    header: "درجة الطالب",
    cell: ({ row }) =>
      h(
        UBadge,
        { class: "capitalize", variant: "subtle", color: "success" },
        () => row.original.score
      ),
  },
  {
    accessorKey: "date",
    id: "تاريخ ووقت الامتحان",
    header: "تاريخ ووقت الامتحان",
    filterFn: "equals",
    cell: ({ row }) =>
      h(() => row.original.exam?.time + " - " + row.original.exam?.date),
  },
];

// Load all exam results for props exam
watch(
  () => props.exam,
  (val) => {
    if (!val) return;
    exam_id.value = { value: props.exam?.id, label: props.exam?.title };
    examResultStore.loadAllExamResults(1, { exam_id: props.exam?.id });
  },
  { immediate: true, deep: true }
);

// For searching inside select menus
const searchHandler = (newVal: string, loadFunction: Function) => {
  if (newVal.length >= 3 || newVal.length < 1) loadFunction(newVal);
};

watch(searchExamTerm, (newVal) =>
  searchHandler(newVal, examStore.loadExamsForSelect)
);
watch(searchStudentTerm, (newVal) =>
  searchHandler(newVal, studentStore.loadStudentsForSelect)
);

// Watch for changes in both exam_id and student_id
watch(
  [() => exam_id.value, () => student_id.value],
  ([examVal, studentVal]) => {
    const params = {};
    if (examVal) {
      params.exam_id = examVal.value;
    } 
    if (studentVal) params.student_id = studentVal.value;

    // Load exam results if there are valid parameters
    examResultStore.loadAllExamResults(1, params);
  },
  { immediate: true, deep: true }
);

// Reset filters
function resetFilters() {
  exam_id.value = null;
  student_id.value = null;
}
</script>

<template>
  <UModal fullscreen v-model:open="open" :title="`درجات الاختبار`">
    <slot />

    <template #body dir="rtl">
      <div class="flex items-center space-x-4 w-full" dir="rtl">
        <!-- Exam Selection -->
        <UFormField
          label="اسم الاختبار"
          placeholder="اسم الاختبار"
          name="exam_id"
        >
          <USelectMenu
            dir="rtl"
            v-model:search-term="searchExamTerm"
            v-model="exam_id"
            :items="examStore.examOptions"
            placeholder="اختر الاختبار"
            :search-input="{
              placeholder: 'بحث...',
              icon: 'i-lucide-search',
            }"
            class="w-62 mb-6"
          />
        </UFormField>

        <!-- Student Selection -->
        <UFormField
          label="اسم الطالب"
          placeholder="اختر الطالب"
          name="student_id"
        >
          <USelectMenu
            dir="rtl"
            v-model:search-term="searchStudentTerm"
            v-model="student_id"
            :items="studentStore.studentOptions"
            placeholder="اختر الطالب"
            :search-input="{
              placeholder: 'بحث...',
              icon: 'i-lucide-search',
            }"
            class="w-62 mb-6"
          />
        </UFormField>

        <!-- Reset Filters Button -->
        <UButton
          icon="i-lucide-x"
          color="gray"
          size="xl"
          @click="resetFilters"
          class="hover:bg-gray-200"
        />
      </div>

      <!-- Exam Results Table -->
      <UTable
        ref="table"
        v-model:pagination="examResultStore.pagination"
        class="shrink-0"
        :data="examResultStore.items"
        :columns="columns"
        :ui="{
          base: 'table-fixed border-separate border-spacing-0',
          thead: '[&>tr]:bg-elevated/50 [&>tr]:after:content-none',
          tbody: '[&>tr]:last:[&>td]:border-b-0',
          th: 'py-2  border-y border-default ',
          td: 'border-b border-default',
        }"
        dir="rtl"
      />
    </template>

    <!-- Footer with Pagination -->
    <template #footer>
      <div class="flex items-center gap-1.5" dir="ltr">
        <UPagination
          dir="ltr"
          :total="examResultStore.pagination?.total"
          :items-per-page="examResultStore.pagination?.pageSize"
          :default-page="examResultStore.pagination?.page"
          @update:page="(p) => examResultStore.loadAllExamResults(p)"
        />
      </div>
    </template>
  </UModal>
</template>
