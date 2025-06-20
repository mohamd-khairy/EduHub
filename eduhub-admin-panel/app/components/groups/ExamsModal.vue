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
const filteredExam = ref([]);

const columns: TableColumn<User>[] = [
  {
    accessorKey: 'id',
    id: 'رقم الاختبار',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()

      return h(UButton, {
        color: 'neutral',
        variant: 'ghost',
        label: 'رقم الاختبار',
        icon: isSorted
          ? isSorted === 'asc'
            ? 'i-lucide-arrow-up-narrow-wide'
            : 'i-lucide-arrow-down-wide-narrow'
          : 'i-lucide-arrow-up-down',
        class: '-mx-2.5',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc')
      })
    }
  },
  {
    accessorKey: 'title',
    id: 'اسم الاختبار',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()

      return h(UButton, {
        color: 'neutral',
        variant: 'ghost',
        label: 'اسم الاختبار',
        icon: isSorted
          ? isSorted === 'asc'
            ? 'i-lucide-arrow-up-narrow-wide'
            : 'i-lucide-arrow-down-wide-narrow'
          : 'i-lucide-arrow-up-down',
        class: '-mx-2.5',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc')
      })
    }
  },
  {
    accessorKey: 'total_marks',
    id: 'درجة الاختبار',
    header: 'درجة الاختبار'
  },
  {
    accessorKey: 'date',
    id: 'تاريخ ووقت الاختبار',
    header: 'تاريخ ووقت الاختبار',
    filterFn: 'equals',
    cell: ({ row }) => {
      const color = 'success'

      return h(UBadge, { class: 'capitalize', variant: 'subtle', color }, () =>
        row.original.time + " - " + row.original.date
      )
    }
  }
]

watch(
  () => exam_id?.value,
  (val) => {
    if (!val) {
      filteredExam.value = props.exams;
    } else {
      filteredExam.value = props.exams.filter((i) => i.id == val.value);
    }
  },
  { immediate: true, deep: true }
);

watch(
  () => props.exams,
  (val) => {
    if (!val) return;

    data.value = props.exams?.map((item) => {
      return {
        label: item.title,
        value: item.id,
      };
    });

    filteredExam.value = props.exams;
  },
  { immediate: true, deep: true }
);
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
          :items="data"
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
      <UTable
        ref="table"
        class="shrink-0"
        :data="filteredExam"
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
