<script setup lang="ts">
const students = ref([]);

const studentStore = useStudentStore();

onMounted(async () => {
  await studentStore.loadAllStudents(1, null, "", 5);
  students.value = studentStore.items;
});

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
</script>

<template>
  <div class="w-full">
    <h2 class="text-xl font-bold mb-2">أحدث طلاب تم انضمامهم للمجموعات</h2>
    <p class="text-sm text-muted mb-4">
      يعرض هذا الجدول أحدث طلاب تم انضمامهم للمجموعات، مما يساعد على تتبع
      التحديثات الأخيرة في المجموعات ويتيح مراقبة الطلاب الجدد وتوزيعهم عبر
      المجموعات.
    </p>

    <UTable
      ref="table"
      class="shrink-0"
      :data="students"
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
</template>
