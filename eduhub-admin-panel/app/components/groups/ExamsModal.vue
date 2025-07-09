<script setup lang="ts">
const props = withDefaults(
  defineProps<{
    exams?: object;
  }>(),
  {
    exams: null,
  }
);

const examStore = useExamStore();

const open = ref(false);
const exam_id = ref(null);
const searchExamTerm = ref("");
const UButton = resolveComponent("UButton");
const UBadge = resolveComponent("UBadge");
const data = ref([]);
// const filteredExam = ref([]);

const columns = [
  {
    accessorKey: "id",
    id: "رقم الاختبار",
    header: ({ column }) => {
      const isSorted = column.getIsSorted();

      return h(UButton, {
        color: "neutral",
        variant: "ghost",
        label: "رقم الاختبار",
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
    accessorKey: "title",
    id: "اسم الاختبار",
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
    accessorKey: "total_marks",
    id: "درجة الاختبار",
    header: "درجة الاختبار",
  },
  {
    accessorKey: "date",
    id: "تاريخ ووقت الاختبار",
    header: "تاريخ ووقت الاختبار",
    filterFn: "equals",
    cell: ({ row }) => {
      const color = "success";

      return h(
        UBadge,
        { class: "capitalize", variant: "subtle", color },
        () => row.original.time + " - " + row.original.date
      );
    },
  },
];

const filteredExam = computed(() => {
  let result = props.exams;

  if (exam_id?.value) {
    result = props.exams.filter((i) => i.id == exam_id.value.value);
  }
  return result;
});

const paginatedExams = ref([]);
const page = ref(1);
const pageCount = ref(10);

function paginateExams() {
  const start = (page.value - 1) * pageCount.value;
  const end = start + pageCount.value;
  paginatedExams.value = filteredExam.value.slice(start, end);
}

watch(filteredExam, () => {
  updatePage(1);
  paginateExams();
});

watch(page, () => {
  paginateExams();
});
function updatePage(p) {
  page.value = p;
}
</script>

<template>
  <UModal fullscreen v-model:open="open" :title="`جميع الاختبارات`">
    <template #body dir="rtl">
      <div
        class="flex items-center space-x-4 mb-6 w-full"
        style="margin-bottom: 10px"
        dir="rtl"
      >
        <USelectMenu
          dir="rtl"
          v-model:search-term="searchExamTerm"
          v-model="exam_id"
          :items="props.exams"
          placeholder="اختر الاختبار"
          :search-input="{
            placeholder: 'بحث...',
            icon: 'i-lucide-search',
          }"
          size="xl"
          class="w-62"
        />

        <UButton
          icon="i-lucide-x"
          color="gray"
          size="sm"
          @click="exam_id = null"
        />
      </div>
      <div class="border rounded-lg overflow-hidden text-lg">
        <UTable
          ref="table"
          class="shrink-0"
          :data="paginatedExams"
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
      </div>
      <div class="flex justify-between items-center mt-4 text-lg">
        <div class="text-gray-500">
          عرض {{ filteredExam.length }} من {{ filteredExam.length }} طالب
        </div>

        <UPagination
          dir="ltr"
          :total="filteredExam.length"
          :items-per-page="pageCount"
          :default-page="page"
          @update:page="(p) => updatePage(p)"
        />
      </div>
    </template>
  </UModal>
</template>
