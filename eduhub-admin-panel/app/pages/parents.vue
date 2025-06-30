<script setup lang="ts">
definePageMeta({
  permission: "read-parentmodel",
});
import type { TableColumn } from "@nuxt/ui";
import { upperFirst } from "scule";
import AddModal from "~/components/parents/AddModal.vue";
import DeleteModal from "~/components/parents/DeleteModal.vue";
import EditModal from "~/components/parents/EditModal.vue";

const parentStore = useParentStore();
const authStore = useAuthStore();

const UButton = resolveComponent("UButton");
const UBadge = resolveComponent("UBadge");
const UDropdownMenu = resolveComponent("UDropdownMenu");
const UCheckbox = resolveComponent("UCheckbox");

const toast = useToast();
const table = useTemplateRef("table");

const columnFilters = ref([
  {
    id: "اسم ولي الامر",
    value: "",
  },
]);

const columnVisibility = ref();

onMounted(() => {
  parentStore.loadAllParents();
});

function getRowItems(row: any) {
  const items = [{ type: "label", label: "الاجراءات" }, { type: "separator" }];

  if (authStore.hasPermission("update-parentmodel")) {
    items.push({
      label: "تعديل ولي الامر",
      icon: "i-lucide-edit",
      color: "primary",
      onSelect() {
        parentStore.editItem = row.original;
        parentStore.editModalOpen = true;
      },
    });
  }

  if (authStore.hasPermission("delete-parentmodel")) {
    items.push({
      label: "حذف ولي الامر",
      icon: "i-lucide-trash",
      color: "error",
      onSelect() {
        parentStore.addId(row.original.id);
        parentStore.deleteModalOpen = true;
      },
    });
  }

  return items;
}

const columns: TableColumn<User>[] = [
  {
    accessorKey: "select",
    id: "اختار",
    header: ({ table }) =>
      h(UCheckbox, {
        modelValue:
          parentStore.selectedIds.length > 0 &&
          parentStore.selectedIds.length ===
            table.getFilteredRowModel().rows.length
            ? true
            : parentStore.selectedIds?.length > 0
            ? "indeterminate"
            : false,
        "onUpdate:modelValue": (value: boolean | "indeterminate") => {
          if (value) {
            // Select all visible rows
            table.getFilteredRowModel().rows.forEach((r) => {
              parentStore.toggleId(r.original.id);
            });
          } else {
            // Deselect all visible rows
            table.getFilteredRowModel().rows.forEach((r) => {
              parentStore.removeId(r.original.id);
            });
          }
        },
        ariaLabel: "Select all",
      }),
    cell: ({ row }) =>
      h(UCheckbox, {
        modelValue: parentStore.selectedIds.includes(row.original.id),
        "onUpdate:modelValue": (value: boolean | "indeterminate") => {
          if (value) {
            parentStore.addId(row.original.id);
          } else {
            parentStore.removeId(row.original.id);
          }
        },
        ariaLabel: "Select row",
      }),
  },
  {
    accessorKey: "id",
    id: "رقم ولي الامر",
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
    id: "اسم ولي الامر",
    header: ({ column }) => {
      const isSorted = column.getIsSorted();

      return h(UButton, {
        color: "neutral",
        variant: "ghost",
        label: "الاسم ",
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
    accessorKey: "phone",
    id: "التليفون",
    header: "التليفون",
    cell: ({ row }) => {
      const color = "success";

      return h(
        UBadge,
        { class: "capitalize", variant: "subtle", color },
        () => row.original.phone
      );
    },
  },
  {
    accessorKey: "students",
    id: "عدد الطلاب",
    header: "عدد الطلاب",
    cell: ({ row }) =>
      h(
        UBadge,
        { class: "capitalize", variant: "subtle", color: "success" },
        () => row.original.students.length
      ),
  },
  {
    accessorKey: "email",
    id: "البريد الالكتروني",
    header: "البريد الالكتروني",
  },
  {
    accessorKey: "address",
    id: "العنوان",
    header: "العنوان",
  },
  {
    accessorKey: "actions",
    id: "الاجراءات",
    header: "الاجراءات",
    cell: ({ row }) => {
      return h(
        "div",
        { class: "text-right" },
        h(
          UDropdownMenu,
          {
            content: {
              align: "end",
            },
            items: getRowItems(row),
          },
          () =>
            h(UButton, {
              icon: "i-lucide-ellipsis-vertical",
              color: "neutral",
              variant: "ghost",
              class: "ml-auto",
            })
        )
      );
    },
  },
];
</script>

<template>
  <UDashboardPanel id="customers">
    <template #header>
      <UDashboardNavbar title="اولياء الامور">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <AddModal />
        </template>

        <DeleteModal
          :count="parentStore.selectedIds.length"
          v-model:open="parentStore.deleteModalOpen"
        />

        <EditModal
          :item="parentStore.editItem"
          v-model:open="parentStore.editModalOpen"
        />
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="flex flex-wrap items-center justify-between gap-1.5">
        <UInput
          :model-value="(table?.tableApi?.getColumn('اسم ولي الامر')?.getFilterValue() as string)"
          class="max-w-sm"
          icon="i-lucide-search"
          placeholder="ابحث ..."
          @update:model-value="
            table?.tableApi?.getColumn('اسم ولي الامر')?.setFilterValue($event)
          "
        />

        <div class="flex flex-wrap items-center gap-1.5">
          <DeleteModal :count="parentStore.selectedIds.length">
            <UButton
              v-if="parentStore.selectedIds.length"
              label="حذف"
              color="error"
              variant="subtle"
              icon="i-lucide-trash"
            >
              <template #trailing>
                <UKbd>
                  {{ parentStore.selectedIds.length }}
                </UKbd>
              </template>
            </UButton>
          </DeleteModal>

          <UButton
            label="تصدير"
            loading-auto
            color="success"
            variant="outline"
            trailing-icon="i-lucide-file-spreadsheet"
            v-if="authStore.hasPermission('read-parentmodel')"
            @click="exportToExcel('parentmodel')"
          />

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
        v-model:pagination="parentStore.pagination"
        class="shrink-0"
        :data="parentStore.items"
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
            :total="parentStore.pagination?.total"
            :items-per-page="parentStore.pagination?.pageSize"
            :default-page="parentStore.pagination?.page"
            @update:page="(p) => parentStore.loadAllParents(p)"
          />
        </div>
      </div>
    </template>
  </UDashboardPanel>
</template>
