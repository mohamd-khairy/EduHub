<script setup lang="ts">
definePageMeta({
  permission: "read-exam",
});
import type { TableColumn } from "@nuxt/ui";
import { upperFirst } from "scule";
import AddModal from "~/components/exams/AddModal.vue";
import DeleteModal from "~/components/exams/DeleteModal.vue";
import EditModal from "~/components/exams/EditModal.vue";
import ExamResultModal from "~/components/exams/ExamResultModal.vue";

const examStore = useExamStore();
const groupStore = useGroupStore();
const examResultStore = useExamResultStore();
const studentStore = useStudentStore();
const authStore = useAuthStore();

const UButton = resolveComponent("UButton");
const UBadge = resolveComponent("UBadge");
const UDropdownMenu = resolveComponent("UDropdownMenu");
const UCheckbox = resolveComponent("UCheckbox");

const toast = useToast();
const table = useTemplateRef("table");

const columnFilters = ref([
  {
    id: "اسم الاختبار",
    value: "",
  },
]);

const columnVisibility = ref();

onMounted(() => {
  examStore.loadAllExams();
  groupStore.loadGroupsForSelect();
  examStore.loadExamsForSelect();
  studentStore.loadStudentsForSelect();
});

// Adjust typing here (replace `User` or your row type)
function getRowItems(row: any) {
  const items = [{ type: "label", label: "الاجراءات" }, { type: "separator" }];

  if (authStore.hasPermission("read-examresult")) {
    items.push({
      label: "مشاهدة درجات الاختبار",
      icon: "i-lucide-list",
      onSelect() {
        examResultStore.showExamResultItem = row.original;
        examResultStore.showExamResultModalOpen = true;
      },
    });
  }
  if (authStore.hasPermission("update-exam")) {
    items.push({
      label: "تعديل الاختبار",
      icon: "i-lucide-edit",
      color: "primary",
      onSelect() {
        examStore.editItem = row.original;
        examStore.editModalOpen = true;
      },
    });
  }
  if (authStore.hasPermission("delete-exam")) {
    items.push({
      label: "حذف الاختبار",
      icon: "i-lucide-trash",
      color: "error",
      onSelect() {
        examStore.addId(row.original.id);
        examStore.deleteModalOpen = true;
      },
    });
  }
  return items;
}

const columns: TableColumn<User>[] = [
  {
    id: "اختار",
    header: ({ table }) =>
      h(UCheckbox, {
        modelValue:
          examStore.selectedIds.length > 0 &&
          examStore.selectedIds.length ===
            table.getFilteredRowModel().rows.length
            ? true
            : examStore.selectedIds?.length > 0
            ? "indeterminate"
            : false,
        "onUpdate:modelValue": (value: boolean | "indeterminate") => {
          if (value) {
            // Select all visible rows
            table.getFilteredRowModel().rows.forEach((r) => {
              examStore.toggleId(r.original.id);
            });
          } else {
            // Deselect all visible rows
            table.getFilteredRowModel().rows.forEach((r) => {
              examStore.removeId(r.original.id);
            });
          }
        },
        ariaLabel: "Select all",
      }),
    cell: ({ row }) =>
      h(UCheckbox, {
        modelValue: examStore.selectedIds.includes(row.original.id),
        "onUpdate:modelValue": (value: boolean | "indeterminate") => {
          if (value) {
            examStore.addId(row.original.id);
          } else {
            examStore.removeId(row.original.id);
          }
        },
        ariaLabel: "Select row",
      }),
  },
  {
    accessorKey: "id",
    id: "رقم الاختبار",
    header: ({ column }) => {
      const isSorted = column.getIsSorted();

      return h(UButton, {
        color: "neutral",
        variant: "ghost",
        label: "رقم الاختبار",
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
    accessorKey: "title",
    id: "اسم الاختبار",
    header: ({ column }) => {
      const isSorted = column.getIsSorted();

      return h(UButton, {
        color: "neutral",
        variant: "ghost",
        label: "اسم الاختبار",
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
    accessorKey: "group",
    id: "اسم المجموعة",
    header: "اسم المجموعة",
    cell: ({ row }) => {
      const color = "warning";

      return h(
        UBadge,
        { class: "capitalize", variant: "subtle", color },
        () => row.original.group?.name
      );
    },
  },
  {
    accessorKey: "total_marks",
    id: "درجة الامتحان",
    header: "درجة الامتحان",
  },
  {
    accessorKey: "date",
    id: "تاريخ ووقت الامتحان",
    header: "تاريخ ووقت الامتحان",
    filterFn: "equals",
    cell: ({ row }) => {
      const color = "success";

      return h(
        UBadge,
        { class: "capitalize", variant: "subtle", color },
        () => row.original.time + " - " + row.original.date
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
      <UDashboardNavbar title="الاختبارات">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <AddModal />
          <Communication />
        </template>

        <DeleteModal
          :count="examStore.selectedIds.length"
          v-model:open="examStore.deleteModalOpen"
        />

        <EditModal
          :item="examStore.editItem"
          v-model:open="examStore.editModalOpen"
        />

        <ExamResultModal
          :exam="examResultStore.showExamResultItem"
          v-model:open="examResultStore.showExamResultModalOpen"
        />
      </UDashboardNavbar>
    </template>

    <template #body v-if="!examStore.isItemLoading">
      <div class="flex flex-wrap items-center justify-between gap-1.5">
        <UInput
          :model-value="(table?.tableApi?.getColumn('اسم الاختبار')?.getFilterValue() as string)"
          class="max-w-sm"
          icon="i-lucide-search"
          placeholder="ابحث ..."
          @update:model-value="
            table?.tableApi?.getColumn('اسم الاختبار')?.setFilterValue($event)
          "
        />

        <div class="flex flex-wrap items-center gap-1.5">
          <DeleteModal :count="examStore.selectedIds.length">
            <UButton
              v-if="examStore.selectedIds.length"
              label="حذف"
              color="error"
              variant="subtle"
              icon="i-lucide-trash"
            >
              <template #trailing>
                <UKbd>
                  {{ examStore.selectedIds.length }}
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
            v-if="authStore.hasPermission('read-exam')"
            @click="exportToExcel('exam')"
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
        v-model:pagination="examStore.pagination"
        class="shrink-0"
        :data="examStore.items"
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
            :total="examStore.pagination?.total"
            :items-per-page="examStore.pagination?.pageSize"
            :default-page="examStore.pagination?.page"
            @update:page="(p) => examStore.loadAllExams(p)"
          />
        </div>
      </div>
    </template>
    <template #body v-else>
      <Loader />
    </template>
  </UDashboardPanel>
</template>
