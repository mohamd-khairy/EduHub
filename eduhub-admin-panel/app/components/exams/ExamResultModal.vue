<script setup lang="ts">

const examResultStore = useExamResultStore()
const examStore = useExamStore()
const studentStore = useStudentStore()


const props = withDefaults(defineProps<{
  exam?: object
}>(), {
  exam: null
})

const table = useTemplateRef('table')

const open = ref(false)

const exam_id = ref(null)
const student_id = ref(null)
const searchExamTerm = ref('')
const searchStudentTerm = ref('')

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')

const columns: TableColumn<User>[] = [
  {
    accessorKey: 'id',
    id: 'رقم الدرجة',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()

      return h(UButton, {
        color: 'neutral',
        variant: 'ghost',
        label: 'رقم الدرجة',
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
    accessorKey: 'exam',
    id: 'اسم الاختبار',
    cell: ({ row }) => {
      const color = 'warning'

      return h(() =>
        row.original.exam?.title
      )
    },
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
    accessorKey: 'student',
    id: 'اسم الطالب',
    header: 'اسم الطالب',
    cell: ({ row }) => {
      const color = 'warning'

      return h(() =>
        row.original.student?.name
      )
    }
  },
  {
    accessorKey: 'total_marks',
    id: 'درجة الامتحان',
    header: 'درجة الامتحان',
    cell: ({ row }) => {
      const color = 'warning'

      return h(UBadge, { class: 'capitalize', variant: 'subtle', color }, () =>
        row.original.exam?.total_marks
      )
    }
  },
  {
    accessorKey: 'score',
    id: 'درجةالطالب',
    header: 'درجةالطالب',
    cell: ({ row }) => {
      const color = 'success'

      return h(UBadge, { class: 'capitalize', variant: 'subtle', color }, () =>
        row.original.score
      )
    }
  },
  {
    accessorKey: 'date',
    id: 'تاريخ ووقت الامتحان',
    header: 'تاريخ ووقت الامتحان',
    filterFn: 'equals',
    cell: ({ row }) => {
      return h(() =>
        row.original.exam?.time + ' - ' + row.original.exam?.date
      )
    }

  }
]

//load all result for props exam
watch(() => props.exam, (val) => {
  if (!val) return
  examResultStore.loadAllExamResults(1, { exam_id: props.exam?.id || 1 })
}, { immediate: true, deep: true })

//for search inside select menus
watch(searchExamTerm, (newVal) => {
  if (newVal.length >= 3 || newVal.length < 1)
    examStore.loadExamsForSelect(newVal)
})

watch(searchStudentTerm, (newVal) => {
  if (newVal.length >= 3 || newVal.length < 1)
    studentStore.loadStudentsForSelect(newVal)
})

//for filtering exam results by exam and student
watch(() => exam_id?.value, (val) => {
  if (!val) return
  examResultStore.loadAllExamResults(1, { exam_id: val?.value, student_id: student_id?.value?.value })
}, { immediate: true, deep: true })

watch(() => student_id?.value, (val) => {
  if (!val) return

  examResultStore.loadAllExamResults(1, { student_id: val?.value, exam_id: exam_id?.value?.value })
}, { immediate: true, deep: true })
</script>

<template>
  <UModal fullscreen v-model:open="open" :title="`درجات الاختبار `">
    <slot />

    <template #body dir="rtl">

      <div class="flex items-center space-x-4 mb-6 w-full" style="margin-bottom: 10px;" dir="rtl">

        <UFormField label="اسم الاختبار" placeholder="اسم الاختبار" name="exam_id">
          <USelectMenu dir="rtl" v-model:search-term="searchExamTerm" v-model="exam_id" :items="examStore.examOptions"
            placeholder="اختر الاختبار" :search-input="{
              placeholder: 'بحث...',
              icon: 'i-lucide-search'
            }" />
        </UFormField>

        <UFormField label="اسم الطالب" placeholder="اختر الطالب" name="student_id">
          <USelectMenu dir="rtl" v-model:search-term="searchStudentTerm" v-model="student_id"
            :items="studentStore.studentOptions" placeholder="اختر الطالب" :search-input="{
              placeholder: 'بحث...',
              icon: 'i-lucide-search'
            }" />
        </UFormField>
      </div>
      <UTable ref="table" v-model:pagination="examResultStore.pagination" class="shrink-0" :data="examResultStore.items"
        :columns="columns" :ui="{
          base: 'table-fixed border-separate border-spacing-0',
          thead: '[&>tr]:bg-elevated/50 [&>tr]:after:content-none',
          tbody: '[&>tr]:last:[&>td]:border-b-0',
          th: 'py-2  border-y border-default ',
          td: 'border-b border-default'
        }" dir="rtl" />

    </template>

    <template #footer>
      <div class="flex items-center gap-1.5" dir="ltr">
        <UPagination dir="ltr" :total="examResultStore.pagination?.total"
          :items-per-page="examResultStore.pagination?.pageSize" :default-page="examResultStore.pagination?.page"
          @update:page="(p) => examResultStore.loadAllExamResults(p)" />
      </div>
    </template>
  </UModal>
</template>
