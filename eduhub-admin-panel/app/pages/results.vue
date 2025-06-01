<script setup lang="ts">
import type { TableColumn } from '@nuxt/ui'
import { upperFirst } from 'scule'
import AddModal from '~/components/results/AddModal.vue'
import DeleteModal from '~/components/results/DeleteModal.vue'
import EditModal from '~/components/results/EditModal.vue'
import type { User } from '~/types'

const examResultStore = useExamResultStore()
const examStore = useExamStore()
const teacherStore = useTeacherStore()

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UCheckbox = resolveComponent('UCheckbox')

const toast = useToast()
const table = useTemplateRef('table')

const columnFilters = ref([{
  id: 'درجةالطالب',
  value: ''
}])

const columnVisibility = ref()

onMounted(() => {
  teacherStore.loadTeachersForSelect()
  examStore.loadExamsForSelect()
  examResultStore.loadAllExamResults(1) //, {exam_id: 1}
})

// Adjust typing here (replace `User` or your row type)
function getRowItems(row: any) {
  return [
    { type: 'label', label: 'الاجراءات' },
    { type: 'separator' },
    {
      label: 'تعديل النتيجة',
      icon: 'i-lucide-edit',
      color: 'primary',
      onSelect() {
        examResultStore.editItem = row.original
        examResultStore.editModalOpen = true
      }
    },
    {
      label: 'حذف النتيجة',
      icon: 'i-lucide-trash',
      color: 'error',
      onSelect() {
        examResultStore.addId(row.original.id)
        examResultStore.deleteModalOpen = true
      }
    }
  ]
}

const columns: TableColumn<User>[] = [
  {
    id: 'اختار',
    header: ({ table }) =>
      h(UCheckbox, {
        modelValue: examResultStore.selectedIds.length > 0 && examResultStore.selectedIds.length === table.getFilteredRowModel().rows.length
          ? true
          : examResultStore.selectedIds?.length > 0
            ? 'indeterminate'
            : false,
        'onUpdate:modelValue': (value: boolean | 'indeterminate') => {
          if (value) {
            // Select all visible rows
            table.getFilteredRowModel().rows.forEach(r => {
              examResultStore.toggleId(r.original.id)
            })
          } else {
            // Deselect all visible rows
            table.getFilteredRowModel().rows.forEach(r => {
              examResultStore.removeId(r.original.id)
            })
          }
        },
        ariaLabel: 'Select all'
      }),
    cell: ({ row }) =>
      h(UCheckbox, {
        modelValue: examResultStore.selectedIds.includes(row.original.id),
        'onUpdate:modelValue': (value: boolean | 'indeterminate') => {
          if (value) {
            examResultStore.addId(row.original.id)
          } else {
            examResultStore.removeId(row.original.id)
          }
        },
        ariaLabel: 'Select row'
      })
  },
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

  },
  {
    accessorKey: 'actions',
    id: 'الاجراءات',
    header: 'الاجراءات',
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
            items: getRowItems(row)
          },
          () =>
            h(UButton, {
              icon: 'i-lucide-ellipsis-vertical',
              color: 'neutral',
              variant: 'ghost',
              class: 'ml-auto'
            })
        )
      )
    }
  }
]
</script>

<template>
  <UDashboardPanel id="customers">
    <template #header>
      <UDashboardNavbar title="الدرجات">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <AddModal />
        </template>

        <DeleteModal :count="examResultStore.selectedIds.length" v-model:open="examResultStore.deleteModalOpen" />

        <EditModal :item="examResultStore.editItem" v-model:open="examResultStore.editModalOpen" />
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="flex flex-wrap items-center justify-between gap-1.5">
        <UInput :model-value="(table?.tableApi?.getColumn('درجةالطالب')?.getFilterValue() as string)" class="max-w-sm"
          icon="i-lucide-search" placeholder="ابحث ..."
          @update:model-value="table?.tableApi?.getColumn('درجةالطالب')?.setFilterValue($event)" />

        <div class="flex flex-wrap items-center gap-1.5">
          <DeleteModal :count="examResultStore.selectedIds.length">
            <UButton v-if="examResultStore.selectedIds.length" label="حذف" color="error" variant="subtle"
              icon="i-lucide-trash">
              <template #trailing>
                <UKbd>
                  {{ examResultStore.selectedIds.length }}
                </UKbd>
              </template>
            </UButton>
          </DeleteModal>

          <UDropdownMenu :items="table?.tableApi
            ?.getAllColumns()
            .filter((column) => column.getCanHide())
            .map((column) => ({
              label: upperFirst(column.id),
              type: 'checkbox' as const,
              checked: column.getIsVisible(),
              onUpdateChecked(checked: boolean) {
                table?.tableApi?.getColumn(column.id)?.toggleVisibility(!!checked)
              },
              onSelect(e?: Event) {
                e?.preventDefault()
              }
            }))
            " :content="{ align: 'end' }">
            <UButton label="الاعمدة" color="neutral" variant="outline" trailing-icon="i-lucide-settings-2" />
          </UDropdownMenu>
        </div>
      </div>

      <UTable ref="table" v-model:column-filters="columnFilters" v-model:column-visibility="columnVisibility"
        v-model:pagination="examResultStore.pagination" class="shrink-0" :data="examResultStore.items"
        :columns="columns" :ui="{
          base: 'table-fixed border-separate border-spacing-0',
          thead: '[&>tr]:bg-elevated/50 [&>tr]:after:content-none',
          tbody: '[&>tr]:last:[&>td]:border-b-0',
          th: 'py-2  border-y border-default ',
          td: 'border-b border-default'
        }" />


      <div class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto" dir="ltr">
        <div class="flex items-center gap-1.5" dir="ltr">
          <UPagination dir="ltr" :total="examResultStore.pagination?.total"
            :items-per-page="examResultStore.pagination?.pageSize" :default-page="examResultStore.pagination?.page"
            @update:page="(p) => examResultStore.loadAllExamResults(p)" />
        </div>
      </div>
    </template>
  </UDashboardPanel>
</template>
