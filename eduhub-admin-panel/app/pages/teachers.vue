<script setup lang="ts">
definePageMeta({
  permission: "read-teacher",
});
import type { TableColumn } from "@nuxt/ui";
import { upperFirst } from "scule";
import AddModal from "~/components/teachers/AddModal.vue";
import DeleteModal from "~/components/teachers/DeleteModal.vue";
import EditModal from "~/components/teachers/EditModal.vue";

const teacherStore = useTeacherStore();
const authStore = useAuthStore();

const UButton = resolveComponent("UButton");
const UBadge = resolveComponent("UBadge");
const UDropdownMenu = resolveComponent("UDropdownMenu");
const UCheckbox = resolveComponent("UCheckbox");

const toast = useToast();
const table = useTemplateRef("table");

const columnFilters = ref([
  {
    id: "اسم المدرس",
    value: "",
  },
]);

const columnVisibility = ref();

onMounted(() => {
  teacherStore.loadAllTeachers();
});

function getRowItems(row: any) {
  const items = [{ type: "label", label: "الاجراءات" }, { type: "separator" }];
  if (authStore.hasPermission("update-teacher")) {
    items.push({
      label: "تعديل المدرس",
      icon: "i-lucide-edit",
      color: "primary",
      onSelect() {
        teacherStore.editItem = row.original;
        teacherStore.editModalOpen = true;
      },
    });
  }
  if (authStore.hasPermission("delete-teacher")) {
    items.push({
      label: "حذف المدرس",
      icon: "i-lucide-trash",
      color: "error",
      onSelect() {
        teacherStore.addId(row.original.id);
        teacherStore.deleteModalOpen = true;
      },
    });
  }
  return items;
}

const columns: TableColumn<object>[] = [
  {
    id: "اختار",
    header: ({ table }) =>
      h(UCheckbox, {
        modelValue:
          teacherStore.selectedIds.length > 0 &&
          teacherStore.selectedIds.length ===
            table.getFilteredRowModel().rows.length
            ? true
            : teacherStore.selectedIds?.length > 0
            ? "indeterminate"
            : false,
        "onUpdate:modelValue": (value: boolean | "indeterminate") => {
          if (value) {
            // Select all visible rows
            table.getFilteredRowModel().rows.forEach((r) => {
              teacherStore.toggleId(r.original.id);
            });
          } else {
            // Deselect all visible rows
            table.getFilteredRowModel().rows.forEach((r) => {
              teacherStore.removeId(r.original.id);
            });
          }
        },
        ariaLabel: "Select all",
      }),
    cell: ({ row }) =>
      h(UCheckbox, {
        modelValue: teacherStore.selectedIds.includes(row.original.id),
        "onUpdate:modelValue": (value: boolean | "indeterminate") => {
          if (value) {
            teacherStore.addId(row.original.id);
          } else {
            teacherStore.removeId(row.original.id);
          }
        },
        ariaLabel: "Select row",
      }),
  },
  {
    accessorKey: "id",
    id: "رقم المدرس",
    header: ({ column }) => {
      const isSorted = column.getIsSorted();

      return h(UButton, {
        color: "neutral",
        variant: "ghost",
        label: "رقم المدرس",
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
    id: "اسم المدرس",
    header: ({ column }) => {
      const isSorted = column.getIsSorted();

      return h(UButton, {
        color: "neutral",
        variant: "ghost",
        label: "اسم المدرس",
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
  },
  {
    accessorKey: "email",
    id: "البريد الالكتروني",
    header: "البريد الالكتروني",
  },
  {
    accessorKey: "groups",
    id: "عدد المجموعات",
    header: "عدد المجموعات",
    cell: ({ row }) =>
      h(
        UBadge,
        { class: "capitalize", variant: "subtle", color: "success" },
        () => row.original.groups.length
      ),
  },
  {
    accessorKey: "specialization",
    id: "اسم التخصص",
    header: "اسم التخصص",
    cell: ({ row }) => {
      const color = "warning";

      return h(
        UBadge,
        { class: "capitalize", variant: "subtle", color },
        () => row.original.specialization
      );
    },
  },
  {
    accessorKey: "salary_amount",
    id: "الراتب",
    header: "الراتب",
    cell: ({ row }) => {
      const color = "success";

      return h(
        UBadge,
        { class: "capitalize", variant: "subtle", color },
        () => row.original.salary_amount
      );
    },
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
      <UDashboardNavbar title="المدرسين">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <AddModal />
          <Communication />
        </template>

        <DeleteModal
          :count="teacherStore.selectedIds.length"
          v-model:open="teacherStore.deleteModalOpen"
        />

        <EditModal
          :item="teacherStore.editItem"
          v-model:open="teacherStore.editModalOpen"
        />
      </UDashboardNavbar>
    </template>

    <template #body v-if="!teacherStore.isItemLoading">
      <div class="flex flex-wrap items-center justify-between gap-1.5">
        <UInput
          :model-value="(table?.tableApi?.getColumn('اسم المدرس')?.getFilterValue() as string)"
          class="max-w-sm"
          icon="i-lucide-search"
          placeholder="ابحث ..."
          @update:model-value="
            table?.tableApi?.getColumn('اسم المدرس')?.setFilterValue($event)
          "
        />

        <div class="flex flex-wrap items-center gap-1.5">
          <DeleteModal :count="teacherStore.selectedIds.length">
            <UButton
              v-if="teacherStore.selectedIds.length"
              label="حذف"
              color="error"
              variant="subtle"
              icon="i-lucide-trash"
            >
              <template #trailing>
                <UKbd>
                  {{ teacherStore.selectedIds.length }}
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
            v-if="authStore.hasPermission('read-teacher')"
            @click="exportToExcel('teacher')"
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
        v-model:pagination="teacherStore.pagination"
        class="shrink-0"
        :data="teacherStore.items"
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
            :total="teacherStore.pagination?.total"
            :items-per-page="teacherStore.pagination?.pageSize"
            :default-page="teacherStore.pagination?.page"
            @update:page="(p) => teacherStore.loadAllTeachers(p)"
          />
        </div>
      </div>
    </template>
    <template #body v-else>
      <Loader />
    </template>
  </UDashboardPanel>
</template>
