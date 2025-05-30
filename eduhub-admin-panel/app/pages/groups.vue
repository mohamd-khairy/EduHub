<script setup lang="ts">
import type { TableColumn } from '@nuxt/ui'
import { upperFirst } from 'scule'
import { ref, computed, onMounted } from 'vue'
import AddModal from '~/components/courses/AddModal.vue'
import DeleteModal from '~/components/courses/DeleteModal.vue'
import type { User } from '~/types'
import { useGroupStore } from '~/stores/groupStore'

const groupStore = useGroupStore()

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UCheckbox = resolveComponent('UCheckbox')

const toast = useToast()
const table = useTemplateRef('table') // Make sure this returns a reactive ref

const columnFilters = ref([{ id: 'name', value: '' }])
const columnVisibility = ref()

onMounted(() => {
  groupStore.loadAllGroups()
})


// Adjust typing here (replace `User` or your row type)
function getRowItems(row: any) {
  return [
    { type: 'label', label: 'الاجراءات' },
    { type: 'separator' },
    { label: 'مشاهدة الطلاب', icon: 'i-lucide-list' },
    { label: 'مشاهدة الامتحانات', icon: 'i-lucide-list' },
    { label: 'مشاهدة المدفوعات', icon: 'i-lucide-wallet' },
    { type: 'separator' },
    {
      label: 'حذف المجموعة',
      icon: 'i-lucide-trash',
      color: 'error',
      onSelect() {
        // groupStore.idsToDelete = [row.original.id]
        groupStore.addId(row.original.id)
        groupStore.deleteModalOpen = true
      }
    }
  ]
}

const columns: TableColumn<User>[] = [
  {
    id: 'select',
    header: ({ table }) =>
      h(UCheckbox, {
        modelValue: groupStore.selectedIds.length > 0 && groupStore.selectedIds.length === table.getFilteredRowModel().rows.length
          ? true
          : groupStore.selectedIds?.length > 0
            ? 'indeterminate'
            : false,
        'onUpdate:modelValue': (value: boolean | 'indeterminate') => {
          if (value) {
            // Select all visible rows
            table.getFilteredRowModel().rows.forEach(r => {
              groupStore.toggleId(r.original.id)
            })
          } else {
            // Deselect all visible rows
            table.getFilteredRowModel().rows.forEach(r => {
              groupStore.removeId(r.original.id)
            })
          }
        },
        ariaLabel: 'Select all'
      }),
    cell: ({ row }) =>
      h(UCheckbox, {
        modelValue: groupStore.selectedIds.includes(row.original.id),
        'onUpdate:modelValue': (value: boolean | 'indeterminate') => {
          if (value) {
            groupStore.addId(row.original.id)
          } else {
            groupStore.removeId(row.original.id)
          }
        },
        ariaLabel: 'Select row'
      })
  },
  {
    accessorKey: 'id',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()
      return h(UButton, {
        color: 'neutral',
        variant: 'ghost',
        label: 'رقم المجموعة',
        icon: isSorted
          ? isSorted === 'asc'
            ? 'i-lucide-arrow-up-narrow-wide'
            : 'i-lucide-arrow-down-wide-narrow'
          : 'i-lucide-arrow-up-down',
        class: '-mx-2.5',
        onClick: () => column.toggleSorting(isSorted === 'asc')
      })
    }
  },
  {
    accessorKey: 'name',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()
      return h(UButton, {
        color: 'neutral',
        variant: 'ghost',
        label: 'اسم المجموعة',
        icon: isSorted
          ? isSorted === 'asc'
            ? 'i-lucide-arrow-up-narrow-wide'
            : 'i-lucide-arrow-down-wide-narrow'
          : 'i-lucide-arrow-up-down',
        class: '-mx-2.5',
        onClick: () => column.toggleSorting(isSorted === 'asc')
      })
    }
  },
  {
    accessorKey: 'course_name',
    header: 'اسم الكورس',
    cell: ({ row }) => h(() => row.original.course.name)
  },
  {
    accessorKey: 'teacher',
    header: 'المدرس',
    cell: ({ row }) =>
      h(
        UBadge,
        { class: 'capitalize', variant: 'subtle', color: 'warning' },
        () => row.original.teacher.name
      )
  },
  {
    accessorKey: 'max_students',
    header: 'عدد للطلاب'
  },
  {
    accessorKey: 'schedule',
    header: 'المواعيد',
    filterFn: 'equals',
    cell: ({ row }) =>
      h(
        UBadge,
        { class: 'capitalize', variant: 'subtle', color: 'success' },
        () => row.original.schedule
      )
  },
  {
    id: 'actions',
    cell: ({ row }) =>
      h(
        'div',
        { class: 'text-right' },
        h(
          UDropdownMenu,
          {
            content: { align: 'end' },
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
]

const selectedIds = computed(() => groupStore.selectedIds)
const count = computed(() => groupStore.selectedIds.length)
</script>

<template>
  <UDashboardPanel id="customers">
    <template #header>
      <UDashboardNavbar title="المجموعات">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <AddModal />
        </template>

        <DeleteModal :count="count" v-model:open="groupStore.deleteModalOpen" />

      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="flex flex-wrap items-center justify-between gap-1.5">
        <UInput :model-value="(table?.value?.tableApi?.getColumn('name')?.getFilterValue() as string)" class="max-w-sm"
          icon="i-lucide-search" placeholder="ابحث ..."
          @update:model-value="table?.value?.tableApi?.getColumn('name')?.setFilterValue($event)" />

        <div class="flex flex-wrap items-center gap-1.5">
          <DeleteModal :count="count">
            <UButton v-if="count" label="حذف" color="error" variant="subtle" icon="i-lucide-trash">
              <template #trailing>
                <UKbd>{{ count }}</UKbd>
              </template>
            </UButton>
          </DeleteModal>

          <UDropdownMenu :items="table?.value?.tableApi
            ?.getAllColumns()
            .filter((column) => column.getCanHide())
            .map((column) => ({
              label: upperFirst(column.id),
              type: 'checkbox',
              checked: column.getIsVisible(),
              onUpdateChecked(checked: boolean) {
                table?.value?.tableApi?.getColumn(column.id)?.toggleVisibility(!!checked)
              },
              onSelect(e?: Event) {
                e?.preventDefault()
              }
            }))" :content="{ align: 'end' }">
            <UButton label="الاعمدة" color="neutral" variant="outline" trailing-icon="i-lucide-settings-2" />
          </UDropdownMenu>
        </div>
      </div>

      <UTable ref="table" v-model:column-filters="columnFilters" v-model:column-visibility="columnVisibility"
        v-model:pagination="groupStore.pagination" class="shrink-0" :data="groupStore.items" :columns="columns" :ui="{
          base: 'table-fixed border-separate border-spacing-0',
          thead: '[&>tr]:bg-elevated/50 [&>tr]:after:content-none',
          tbody: '[&>tr]:last:[&>td]:border-b-0',
          th: 'py-2  border-y border-default ',
          td: 'border-b border-default'
        }" />

      <div class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto">
        <div class="flex items-center gap-1.5" dir="ltr">
          <UPagination dir="ltr" :total="groupStore?.pagination?.total"
            :items-per-page="groupStore?.pagination?.pageSize" :default-page="groupStore?.pagination?.page"
            @update:page="(p) => groupStore.loadGroups(p)" />
        </div>
      </div>
    </template>
  </UDashboardPanel>
</template>
