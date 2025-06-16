<script setup lang="ts">
definePageMeta({
  permission: "read-course",
});
import type { TableColumn } from '@nuxt/ui'
import { upperFirst } from 'scule'
import AddModal from '~/components/courses/AddModal.vue'
import DeleteModal from '~/components/courses/DeleteModal.vue'
import EditModal from '~/components/courses/EditModal.vue'
import type { User } from '~/types'

const courseStore = useCourseStore()

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UCheckbox = resolveComponent('UCheckbox')
const toast = useToast()
const table = useTemplateRef('table')

const columnFilters = ref([{
  id: 'اسم الكورس',
  value: ''
}])

const columnVisibility = ref()

onMounted(() => {
  courseStore.loadAllCourses()
})

function getRowItems(row: any) {
  return [
    { type: 'label', label: 'الاجراءات' },
    { type: 'separator' },
    {
      label: 'تعديل الكورس',
      icon: 'i-lucide-edit',
      color: 'primary',
      onSelect() {
        courseStore.editItem = row.original
        courseStore.editModalOpen = true
      }
    },
    {
      label: 'حذف الكورس',
      icon: 'i-lucide-trash',
      color: 'error',
      onSelect() {
        courseStore.addId(row.original.id)
        courseStore.deleteModalOpen = true
      }
    }
  ]
}

const columns: TableColumn<User>[] = [
  {
    id: 'اختار',
    header: ({ table }) =>
      h(UCheckbox, {
        modelValue: courseStore.selectedIds.length > 0 && courseStore.selectedIds.length === table.getFilteredRowModel().rows.length
          ? true
          : courseStore.selectedIds?.length > 0
            ? 'indeterminate'
            : false,
        'onUpdate:modelValue': (value: boolean | 'indeterminate') => {
          if (value) {
            // Select all visible rows
            table.getFilteredRowModel().rows.forEach(r => {
              courseStore.toggleId(r.original.id)
            })
          } else {
            // Deselect all visible rows
            table.getFilteredRowModel().rows.forEach(r => {
              courseStore.removeId(r.original.id)
            })
          }
        },
        ariaLabel: 'Select all'
      }),
    cell: ({ row }) =>
      h(UCheckbox, {
        modelValue: courseStore.selectedIds.includes(row.original.id),
        'onUpdate:modelValue': (value: boolean | 'indeterminate') => {
          if (value) {
            courseStore.addId(row.original.id)
          } else {
            courseStore.removeId(row.original.id)
          }
        },
        ariaLabel: 'Select row'
      })
  },
  {
    id: 'رقم الكورس',
    accessorKey: 'id',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()

      return h(UButton, {
        color: 'neutral',
        variant: 'ghost',
        label: 'رقم الكورس',
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
    id: 'اسم الكورس',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()

      return h(UButton, {
        color: 'neutral',
        variant: 'ghost',
        label: 'اسم الكورس',
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
    accessorKey: 'groups',
    id: 'عدد المجموعات',
    header: 'عدد المجموعات',
    cell: ({ row }) =>
      h(
        UBadge,
        { class: 'capitalize', variant: 'subtle', color: 'success' },
        () => (row.original.groups).length
      )
  },
  {
    accessorKey: 'description',
    id: 'تفاصيل الكورس',
    header: 'تفاصيل الكورس',
    cell: ({ row }) =>
      h(
        () => row.original.description
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
      <UDashboardNavbar title="الكورسات">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <AddModal />
        </template>


        <DeleteModal :count="courseStore.selectedIds.length" v-model:open="courseStore.deleteModalOpen" />

        <EditModal :item="courseStore.editItem" v-model:open="courseStore.editModalOpen" />

      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="flex flex-wrap items-center justify-between gap-1.5">
        <UInput :model-value="(table?.tableApi?.getColumn('اسم الكورس')?.getFilterValue() as string)" class="max-w-sm"
          icon="i-lucide-search" placeholder="ابحث ..."
          @update:model-value="table?.tableApi?.getColumn('اسم الكورس')?.setFilterValue($event)" />

        <div class="flex flex-wrap items-center gap-1.5">
          <DeleteModal :count="courseStore.selectedIds.length">
            <UButton v-if="courseStore.selectedIds.length" label="حذف" color="error" variant="subtle"
              icon="i-lucide-trash">
              <template #trailing>
                <UKbd>
                  {{ courseStore.selectedIds.length }}
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
        v-model:pagination="courseStore.pagination" class="shrink-0" :data="courseStore.items" :columns="columns" :ui="{
          base: 'table-fixed border-separate border-spacing-0',
          thead: '[&>tr]:bg-elevated/50 [&>tr]:after:content-none',
          tbody: '[&>tr]:last:[&>td]:border-b-0',
          th: 'py-2  border-y border-default ',
          td: 'border-b border-default'
        }" />


      <div class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto" dir="ltr">
        <div class="flex items-center gap-1.5" dir="ltr">
          <UPagination dir="ltr" :total="courseStore.pagination?.total"
            :items-per-page="courseStore.pagination?.pageSize" :default-page="courseStore.pagination?.page"
            @update:page="(p) => courseStore.loadAllCourses(p)" />
        </div>
      </div>
    </template>
  </UDashboardPanel>
</template>
