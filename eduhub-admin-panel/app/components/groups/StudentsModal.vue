<script setup lang="ts">
const props = withDefaults(
  defineProps<{
    students?: object;
  }>(),
  {
    students: null,
  }
);

const studentStore = useStudentStore();

const open = ref(false);
const student_id = ref(null);
const searchStudentTerm = ref("");
const UButton = resolveComponent("UButton");
const UBadge = resolveComponent("UBadge");
const data = ref([]);
const AllStudents = ref([]);

const columns = [
  {
    accessorKey: "id",
    id: "الرقم ",
    header: ({ column }) => {
      const isSorted = column.getIsSorted();

      return h(UButton, {
        color: "neutral",
        variant: "ghost",
        label: "الرقم ",
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
    accessorKey: "name",
    id: "اسم الطالب",
    cell: ({ row }) => {
      const color = "warning";

      return h(() => row.original?.name);
    },
    header: ({ column }) => {
      const isSorted = column.getIsSorted();

      return h(UButton, {
        color: "neutral",
        variant: "ghost",
        label: "اسم الطالب",
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
    accessorKey: "gender",
    id: "نوع الطالب",
    header: "نوع الطالب",
    cell: ({ row }) => {
      const color = "warning";

      return h(() => row.original.gender);
    },
  },
  {
    accessorKey: "grade_level",
    id: "مستوي الطالب ",
    header: "مستوي الطالب ",
    cell: ({ row }) => {
      const color = "warning";
      return h(
        UBadge,
        { class: "capitalize", variant: "subtle", color },
        () => row.original?.grade_level
      );
    },
  },
  {
    accessorKey: "parent",
    id: "ولي امر الطالب ",
    header: "ولي امر الطالب ",
    cell: ({ row }) => {
      const color = "success";
      return h(
        UBadge,
        { class: "capitalize", variant: "subtle", color },
        () => row.original?.parent?.name
      );
    },
  },
  {
    accessorKey: "school_name",
    id: "مدرسة الطالب",
    header: "مدرسة الطالب",
    cell: ({ row }) => {
      return h(() => row.original.school_name);
    },
  },
  {
    accessorKey: "phone",
    id: "تليفون الطالب",
    header: "تليفون الطالب",
    filterFn: "equals",
    cell: ({ row }) => {
      return h(() => row.original.phone);
    },
  },
  {
    accessorKey: "email",
    id: "البريد الالكتروني ",
    header: "البريد الالكتروني ",
    filterFn: "equals",
    cell: ({ row }) => {
      return h(() => row.original.email);
    },
  },
];

const paginatedStudents = ref([]);
const page = ref(1);
const pageCount = ref(10);
const searchQuery = ref("");

const filteredStudent = computed(() => {
  let result = props.students;

  if (searchQuery.value) {
    // updatePage(1)
    const query = searchQuery.value.toLowerCase();
    result = result.filter((s) => s.name.toLowerCase().includes(query));
  }

  return result;
});

function paginateStudents() {
  const start = (page.value - 1) * pageCount.value;
  const end = start + pageCount.value;
  paginatedStudents.value = filteredStudent.value.slice(start, end);
}

watch(filteredStudent, () => {
  updatePage(1);
  paginateStudents();
});

watch(page, () => {
  paginateStudents();
});

function updatePage(p) {
  page.value = p;
}
</script>

<template>
  <UModal fullscreen v-model:open="open" :title="`جميع الطلاب`">
    <template #body dir="rtl" v-if="paginatedStudents.length > 0">
      <div
        class="flex items-center space-x-4 mb-6 w-full"
        style="margin-bottom: 10px"
        dir="rtl"
      >
        <UInput
          class="max-w-sm"
          icon="i-lucide-search"
          placeholder="ابحث ..."
          v-model="searchQuery"
        />

        <UButton
          icon="i-lucide-x"
          color="gray"
          size="sm"
          @click="searchQuery = null"
          class="hover:bg-gray-200 focus:bg-gray-200 active:bg-gray-300"
        />
      </div>
      <div class="border rounded-lg overflow-hidden text-lg">
        <UTable
          ref="table"
          class="shrink-0"
          :data="paginatedStudents"
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
      <!-- Pagination -->
      <div class="flex justify-between items-center mt-4 text-lg">
        <div class="text-gray-500">
          عرض {{ paginatedStudents.length }} من
          {{ filteredStudent.length }} طالب
        </div>

        <UPagination
          dir="ltr"
          :total="filteredStudent.length"
          :items-per-page="pageCount"
          :default-page="page"
          @update:page="(p) => updatePage(p)"
        />
      </div>
    </template>
  </UModal>
</template>
