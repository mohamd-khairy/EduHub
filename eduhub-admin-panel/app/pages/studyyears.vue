<script setup lang="ts">
definePageMeta({
  permission: "read-studyyear",
});
import type { TableColumn } from '@nuxt/ui'
import { upperFirst } from 'scule'
import AddModal from '~/components/studyYears/AddModal.vue'
import DeleteModal from '~/components/studyYears/DeleteModal.vue'
import EditModal from '~/components/studyYears/EditModal.vue'
import type { User } from '~/types'

const studyYearStore = useStudyYearStore()
const authStore = useAuthStore();

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UCheckbox = resolveComponent('UCheckbox')
const toast = useToast()
const table = useTemplateRef('table')

const columnFilters = ref([{
  id: 'اسم السنة الدراسية',
  value: ''
}])

const columnVisibility = ref()

onMounted(() => {
  studyYearStore.loadAllStudyYears()
})

function getRowItems(row: any) {
  const items = [
    { type: 'label', label: 'الاجراءات' },
    { type: 'separator' },
  ]

  if (authStore.hasPermission('update-studyyear')) {
    items.push({
      label: 'تعديل السنة الدراسية',
      icon: 'i-lucide-edit',
      color: 'primary',
      onSelect() {
        studyYearStore.editItem = row.original
        studyYearStore.editModalOpen = true
      }
    })
  }

  if (authStore.hasPermission('delete-studyyear')) {
    items.push({
      label: 'حذف السنة الدراسية',
      icon: 'i-lucide-trash',
      color: 'error',
      onSelect() {
        studyYearStore.addId(row.original.id)
        studyYearStore.deleteModalOpen = true
      }
    })
  }

  return items
}

const columns: TableColumn<User>[] = [
  {
    id: 'اختار',
    header: ({ table }) =>
      h(UCheckbox, {
        modelValue: studyYearStore.selectedIds.length > 0 && studyYearStore.selectedIds.length === table.getFilteredRowModel().rows.length
          ? true
          : studyYearStore.selectedIds?.length > 0
            ? 'indeterminate'
            : false,
        'onUpdate:modelValue': (value: boolean | 'indeterminate') => {
          if (value) {
            // Select all visible rows
            table.getFilteredRowModel().rows.forEach(r => {
              studyYearStore.toggleId(r.original.id)
            })
          } else {
            // Deselect all visible rows
            table.getFilteredRowModel().rows.forEach(r => {
              studyYearStore.removeId(r.original.id)
            })
          }
        },
        ariaLabel: 'Select all'
      }),
    cell: ({ row }) =>
      h(UCheckbox, {
        modelValue: studyYearStore.selectedIds.includes(row.original.id),
        'onUpdate:modelValue': (value: boolean | 'indeterminate') => {
          if (value) {
            studyYearStore.addId(row.original.id)
          } else {
            studyYearStore.removeId(row.original.id)
          }
        },
        ariaLabel: 'Select row'
      })
  },
  {
    id: 'رقم السنة الدراسية',
    accessorKey: 'id',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()

      return h(UButton, {
        color: 'neutral',
        variant: 'ghost',
        label: 'رقم السنة الدراسية',
        icon: isSorted
          ? isSorted === 'asc'
            ? 'i-lucide-arrow-up-narrow-wide'
            : 'i-lucide-arrow-down-wide-narrow'
          : 'i-lucide-arrow-up-down',
        class: '-mx-2.5',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc')
      })
    },
    cell: ({ row }) =>
      h(
        () => row.original.id
      )
  },
  {
    accessorKey: 'name',
    id: 'اسم السنة الدراسية',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()

      return h(UButton, {
        color: 'neutral',
        variant: 'ghost',
        label: 'اسم السنة الدراسية',
        icon: isSorted
          ? isSorted === 'asc'
            ? 'i-lucide-arrow-up-narrow-wide'
            : 'i-lucide-arrow-down-wide-narrow'
          : 'i-lucide-arrow-up-down',
        class: '-mx-2.5',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc')
      })
    },
    cell: ({ row }) =>
      h(
        () => row.original.name
      )
  },
  {
    accessorKey: 'start_date',
    id: 'تاريخ بدء السنة الدراسية',
    header: 'تاريخ بدء السنة الدراسية',
    cell: ({ row }) =>
      h(

        () => row.original.start_date
      )
  },
   {
    accessorKey: 'end_date',
    id: 'تاريخ نهاية السنة الدراسية',
    header: 'تاريخ نهاية السنة الدراسية',
    cell: ({ row }) =>
      h(

        () => row.original.end_date
      )
  },
   {
    accessorKey: 'status',
    id: 'حالة السنة الدراسية',
    header: 'حالة السنة الدراسية',
    cell: ({ row }) =>
      h(
        UBadge,
        { class: 'capitalize', variant: 'subtle', color: 'success' },
        () => row.original.status ? 'مفعلة' : 'غير مفعلة'
      )
  },
  {
    accessorKey: 'الاجراءات',
    id: 'الاجراءات',
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
      <UDashboardNavbar title="السنة الدراسية">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <AddModal />
        </template>

        <DeleteModal
          :count="studyYearStore.selectedIds.length"
          v-model:open="studyYearStore.deleteModalOpen"
        />

        <EditModal
          :item="studyYearStore.editItem"
          v-model:open="studyYearStore.editModalOpen"
        />
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="flex flex-wrap items-center justify-between gap-1.5">
        <UInput
          :model-value="(table?.tableApi?.getColumn('اسم السنة الدراسية')?.getFilterValue() as string)"
          class="max-w-sm"
          icon="i-lucide-search"
          placeholder="ابحث ..."
          @update:model-value="table?.tableApi?.getColumn('اسم السنة الدراسية')?.setFilterValue($event)"
        />

        <div class="flex flex-wrap items-center gap-1.5">
          <DeleteModal :count="studyYearStore.selectedIds.length">
            <UButton
              v-if="studyYearStore.selectedIds.length"
              label="حذف"
              color="error"
              variant="subtle"
              icon="i-lucide-trash"
            >
              <template #trailing>
                <UKbd>
                  {{ studyYearStore.selectedIds.length }}
                </UKbd>
              </template>
            </UButton>
          </DeleteModal>

          <UDropdownMenu
            :items="table?.tableApi
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
            "
            :content="{ align: 'end' }"
          >
            <UButton
              label="الاعمدة"
              color="neutral"
              variant="outline"
              trailing-icon="i-lucide-settings-2"
            />
          </UDropdownMenu>
        </div>
      </div>

      <UTable
        ref="table"
        v-model:column-filters="columnFilters"
        v-model:column-visibility="columnVisibility"
        v-model:pagination="studyYearStore.pagination"
        class="shrink-0"
        :data="studyYearStore.items"
        :columns="columns"
        :ui="{
          base: 'table-fixed border-separate border-spacing-0',
          thead: '[&>tr]:bg-elevated/50 [&>tr]:after:content-none',
          tbody: '[&>tr]:last:[&>td]:border-b-0',
          th: 'py-2  border-y border-default ',
          td: 'border-b border-default',
        }"
      />

      <div
        class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto"
        dir="ltr"
      >
        <div class="flex items-center gap-1.5" dir="ltr">
          <UPagination
            dir="ltr"
            :total="studyYearStore.pagination?.total"
            :items-per-page="studyYearStore.pagination?.pageSize"
            :default-page="studyYearStore.pagination?.page"
            @update:page="(p) => studyYearStore.loadAllStudyYears(p)"
          />
        </div>
      </div>
    </template>
  </UDashboardPanel>
</template>
