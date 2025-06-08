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
const filteredStudent = ref([]);

const columns: TableColumn<User>[] = [
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

watch(
  () => student_id?.value,
  (val) => {
    if (!val) {
      filteredStudent.value = props.students;
    } else {
      filteredStudent.value = props.students.filter((i) => i.id == val.value);
    }
  },
  { immediate: true, deep: true }
);

watch(
  () => props.students,
  (val) => {
    if (!val) return;

    data.value = props.students?.map((item) => {
      return {
        label: item.name,
        value: item.id,
      };
    });

    filteredStudent.value = props.students;
  },
  { immediate: true, deep: true }
);
</script>

<template>
  <UModal fullscreen v-model:open="open" :title="`جميع الطلاب`">
    <template #body dir="rtl">
      <div
        class="flex items-center space-x-4 mb-6 w-full"
        style="margin-bottom: 10px"
        dir="rtl"
      >
        <USelectMenu
          dir="rtl"
          v-model:search-term="searchStudentTerm"
          v-model="student_id"
          :items="data"
          placeholder="اختر الطالب"
          :search-input="{
            placeholder: 'بحث...',
            icon: 'i-lucide-search',
          }"
          size="xl"
        />

        <UButton
          icon="i-lucide-x"
          color="gray"
          size="sm"
          @click="student_id = null"
        />
      </div>
      <UTable
        ref="table"
        class="shrink-0"
        :data="filteredStudent"
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
  </UModal>
</template>
