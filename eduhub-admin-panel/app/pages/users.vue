<script setup lang="ts">
import type { TableColumn } from '@nuxt/ui'
import { upperFirst } from 'scule'
import AddModal from '~/components/users/AddModal.vue'
import DeleteModal from '~/components/users/DeleteModal.vue'
import EditModal from '~/components/users/EditModal.vue'

const userStore = useUserStore();

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UCheckbox = resolveComponent('UCheckbox')

const toast = useToast()
const table = useTemplateRef('table')

const columnFilters = ref([{
  id: 'الاسم',
  value: ''
}])

const columnVisibility = ref()

const items = ref([])

onMounted(async () => {
  await userStore.loadAllUsers()
  items.value = userStore.items;
})

function getRowItems(row: any) {
  return [
    { type: 'label', label: 'الاجراءات' },
    { type: 'separator' },
    {
      label: 'تعديل الموظف',
      icon: 'i-lucide-edit',
      color: 'primary',
      onSelect() {
        userStore.editItem = row.original
        userStore.editModalOpen = true
      }
    },
    {
      label: 'حذف الموظف',
      icon: 'i-lucide-trash',
      color: 'error',
      onSelect() {
        userStore.addId(row.original.id)
        userStore.deleteModalOpen = true
      }
    }
  ]
}

const columns: TableColumn<object>[] = [
  {
    id: 'اختار',
    header: ({ table }) =>
      h(UCheckbox, {
        modelValue: userStore.selectedIds.length > 0 && userStore.selectedIds.length === table.getFilteredRowModel().rows.length
          ? true
          : userStore.selectedIds?.length > 0
            ? 'indeterminate'
            : false,
        'onUpdate:modelValue': (value: boolean | 'indeterminate') => {
          if (value) {
            // Select all visible rows
            table.getFilteredRowModel().rows.forEach(r => {
              userStore.toggleId(r.original.id)
            })
          } else {
            // Deselect all visible rows
            table.getFilteredRowModel().rows.forEach(r => {
              userStore.removeId(r.original.id)
            })
          }
        },
        ariaLabel: 'Select all'
      }),
    cell: ({ row }) =>
      h(UCheckbox, {
        modelValue: userStore.selectedIds.includes(row.original.id),
        'onUpdate:modelValue': (value: boolean | 'indeterminate') => {
          if (value) {
            userStore.addId(row.original.id)
          } else {
            userStore.removeId(row.original.id)
          }
        },
        ariaLabel: 'Select row'
      })
  },
  {
    accessorKey: 'id',
    id: 'الرقم',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()

      return h(UButton, {
        color: 'neutral',
        variant: 'ghost',
        label: 'الرقم ',
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
    accessorKey: 'image',
    id: 'الصورة',
    header: 'الصورة',
    cell: ({ row }) => {
      const imageUrl = row.original.image; // fallback image
      return h('img', {
        src: imageUrl,
        alt: row.original.name || 'User Image',
        class: 'w-10 h-10 rounded-full object-cover'
      });
    }
  },
  {
    accessorKey: 'name',
    id: 'الاسم',
    header: ({ column }) => {
      const isSorted = column.getIsSorted()

      return h(UButton, {
        color: 'neutral',
        variant: 'ghost',
        label: 'الاسم ',
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
    accessorKey: 'email',
    id: 'البريد الالكتروني',
    header: 'البريد الالكتروني',
  },
  {
    accessorKey: 'phone',
    id: ' رقم الهاتف ',
    header: ' رقم الهاتف '
  },
  {
    accessorKey: 'display_roles',
    id: 'الدور',
    header: 'الدور',
    cell: ({ row }) => {
      const color = 'success'

      return h(UBadge, { class: 'capitalize', variant: 'subtle', color }, () =>
        row.original.display_roles
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

watch(() => userStore.items, () => {
  items.value = userStore.items;
})
</script>

<template>
  <UDashboardPanel id="customers">
    <template #header>
      <UDashboardNavbar title="الموظفيين">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <AddModal />

          <DeleteModal :count="userStore.selectedIds.length" v-model:open="userStore.deleteModalOpen" />

          <EditModal :item="userStore.editItem" v-model:open="userStore.editModalOpen" />

        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="flex flex-wrap items-center justify-between gap-1.5">
        <UInput :model-value="(table?.tableApi?.getColumn('الاسم')?.getFilterValue() as string)" class="max-w-sm"
          icon="i-lucide-search" placeholder="ابحث ..."
          @update:model-value="table?.tableApi?.getColumn('الاسم')?.setFilterValue($event)" />

        <div class="flex flex-wrap items-center gap-1.5">
          <DeleteModal :count="userStore.selectedIds.length">
            <UButton v-if="userStore.selectedIds.length" label="حذف" color="error" variant="subtle"
              icon="i-lucide-trash">
              <template #trailing>
                <UKbd>
                  {{ userStore.selectedIds.length }}
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
        v-model:pagination="userStore.pagination" class="shrink-0" :data="items" :columns="columns" :ui="{
          base: 'table-fixed border-separate border-spacing-0',
          thead: '[&>tr]:bg-elevated/50 [&>tr]:after:content-none',
          tbody: '[&>tr]:last:[&>td]:border-b-0',
          th: 'py-2  border-y border-default ',
          td: 'border-b border-default'
        }" />


      <div class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto">
        <div class="flex items-center gap-1.5" dir="rtl">
          <UPagination dir="rtl" :total="userStore?.pagination?.total" :items-per-page="userStore?.pagination?.pageSize"
            :default-page="userStore?.pagination?.page" @update:page="(p) => userStore.loadAllUsers(p)" />
        </div>
      </div>
    </template>
  </UDashboardPanel>
</template>
