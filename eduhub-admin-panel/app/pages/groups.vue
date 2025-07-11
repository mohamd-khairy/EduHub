<script setup lang="ts">
definePageMeta({
  permission: "read-group",
});
import type { TableColumn } from "@nuxt/ui";
import { upperFirst } from "scule";
import { ref, computed, onMounted } from "vue";
import AddModal from "~/components/groups/AddModal.vue";
import DeleteModal from "~/components/groups/DeleteModal.vue";
import EditModal from "~/components/groups/EditModal.vue";
import StudentsModal from "~/components/groups/StudentsModal.vue";
import ExamsModal from "~/components/groups/ExamsModal.vue";

const groupStore = useGroupStore();
const courseStore = useCourseStore();
const teacherStore = useTeacherStore();
const authStore = useAuthStore();

const UButton = resolveComponent("UButton");
const UBadge = resolveComponent("UBadge");
const UDropdownMenu = resolveComponent("UDropdownMenu");
const UCheckbox = resolveComponent("UCheckbox");

const toast = useToast();
const table = useTemplateRef("table"); // Make sure this returns a reactive ref

const columnFilters = ref([{ id: "اسم المجموعة", value: "" }]);
const columnVisibility = ref();

function getRowItems(row: any) {
  const items = [{ type: "label", label: "الاجراءات" }, { type: "separator" }];

  if (authStore.hasPermission("read-student")) {
    items.push({
      label: "مشاهدة الطلاب",
      icon: "i-lucide-users",
      onSelect() {
        groupStore.selectedItem = row.original;
        groupStore.showGroupStudentsModal = true;
      },
    });
  }
  if (authStore.hasPermission("read-exam")) {
    items.push({
      label: "مشاهدة الاختبارات",
      icon: "i-lucide-list",
      onSelect() {
        groupStore.selectedItem = row.original;
        groupStore.showGroupExamsModal = true;
      },
    });
  }
  if (authStore.hasPermission("update-group")) {
    items.push({
      label: "تعديل المجموعة",
      icon: "i-lucide-edit",
      color: "primary",
      onSelect() {
        groupStore.editItem = row.original;
        groupStore.editModalOpen = true;
      },
    });
  }
  if (authStore.hasPermission("delete-group")) {
    items.push({
      label: "حذف المجموعة",
      icon: "i-lucide-trash",
      color: "error",
      onSelect() {
        groupStore.addId(row.original.id);
        groupStore.deleteModalOpen = true;
      },
    });
  }
  return items;
}

const columns: TableColumn[] = [
  {
    id: "اختار",
    header: ({ table }) =>
      h(UCheckbox, {
        modelValue:
          groupStore.selectedIds.length > 0 &&
          groupStore.selectedIds.length ===
            table.getFilteredRowModel().rows.length
            ? true
            : groupStore.selectedIds?.length > 0
            ? "indeterminate"
            : false,
        "onUpdate:modelValue": (value: boolean | "indeterminate") => {
          if (value) {
            // Select all visible rows
            table.getFilteredRowModel().rows.forEach((r) => {
              groupStore.toggleId(r.original.id);
            });
          } else {
            // Deselect all visible rows
            table.getFilteredRowModel().rows.forEach((r) => {
              groupStore.removeId(r.original.id);
            });
          }
        },
        ariaLabel: "Select all",
      }),
    cell: ({ row }) =>
      h(UCheckbox, {
        modelValue: groupStore.selectedIds.includes(row.original.id),
        "onUpdate:modelValue": (value: boolean | "indeterminate") => {
          if (value) {
            groupStore.addId(row.original.id);
          } else {
            groupStore.removeId(row.original.id);
          }
        },
        ariaLabel: "Select row",
      }),
  },
  {
    accessorKey: "id",
    id: "رقم المجموعة",
    header: ({ column }) => {
      const isSorted = column.getIsSorted();
      return h(UButton, {
        color: "neutral",
        variant: "ghost",
        label: "رقم المجموعة",
        icon: isSorted
          ? isSorted === "asc"
            ? "i-lucide-arrow-up-narrow-wide"
            : "i-lucide-arrow-down-wide-narrow"
          : "i-lucide-arrow-up-down",
        class: "-mx-2.5",
        onClick: () => column.toggleSorting(isSorted === "asc"),
      });
    },
    cell: ({ row }) => h(() => row.original.id),
  },
  {
    accessorKey: "name",
    id: "اسم المجموعة",
    header: ({ column }) => {
      const isSorted = column.getIsSorted();
      return h(UButton, {
        color: "neutral",
        variant: "ghost",
        label: "اسم المجموعة",
        icon: isSorted
          ? isSorted === "asc"
            ? "i-lucide-arrow-up-narrow-wide"
            : "i-lucide-arrow-down-wide-narrow"
          : "i-lucide-arrow-up-down",
        class: "-mx-2.5",
        onClick: () => column.toggleSorting(isSorted === "asc"),
      });
    },
    cell: ({ row }) => h(() => row.original.name),
  },
  {
    accessorKey: "course_name",
    id: "اسم الكورس",
    header: "اسم الكورس",
    cell: ({ row }) => h(() => row.original.course?.name),
  },
  {
    accessorKey: "اسم المدرس",
    header: "اسم المدرس",
    cell: ({ row }) =>
      h(
        UBadge,
        { class: "capitalize", variant: "subtle", color: "warning" },
        () => row.original.teacher?.name
      ),
  },
  {
    accessorKey: "students_count",
    id: "عدد الطلاب",
    header: ({ column }) => {
      const isSorted = column.getIsSorted();
      return h(UButton, {
        color: "neutral",
        variant: "ghost",
        label: "عدد للطلاب",
        icon: isSorted
          ? isSorted === "asc"
            ? "i-lucide-arrow-up-narrow-wide"
            : "i-lucide-arrow-down-wide-narrow"
          : "i-lucide-arrow-up-down",
        class: "-mx-2.5",
        onClick: () => column.toggleSorting(isSorted === "asc"),
      });
    },
    cell: ({ row }) => h(() => row.original.students_count),
  },
  {
    accessorKey: "schedule",
    id: "المواعيد",
    header: "المواعيد",
    filterFn: "equals",
    cell: ({ row }) =>
      h(
        UBadge,
        { class: "capitalize", variant: "subtle", color: "success" },
        () => row.original.schedule || "لا يوجد مواعيد محددة"
      ),
  },
  {
    accessorKey: "study_year_id",
    id: "السنة الدراسية",
    header: "السنة الدراسية",
    filterFn: "equals",
    cell: ({ row }) =>
      h(
        UBadge,
        { class: "capitalize", variant: "subtle", color: "error" },
        () => row.original.study_year?.name || "لا يوجد سنة دراسية محددة"
      ),
  },
  {
    accessorKey: "actions",
    id: "الاجراءات",
    header: "الاجراءات",
    cell: ({ row }) =>
      h(
        "div",
        { class: "text-right" },
        h(
          UDropdownMenu,
          {
            content: { align: "end" },
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
      ),
  },
];

onMounted(() => {
  teacherStore.loadTeachersForSelect();
  courseStore.loadCoursesForSelect();
  groupStore.loadAllGroups();
});
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
          <Communication />
        </template>

        <DeleteModal
          :count="groupStore.selectedIds.length"
          v-model:open="groupStore.deleteModalOpen"
        />

        <EditModal
          :item="groupStore.editItem"
          v-model:open="groupStore.editModalOpen"
        />

        <StudentsModal
          :students="groupStore.selectedItem?.students"
          v-model:open="groupStore.showGroupStudentsModal"
        />

        <ExamsModal
          :exams="groupStore.selectedItem?.exams"
          v-model:open="groupStore.showGroupExamsModal"
        />
      </UDashboardNavbar>
    </template>

    <template #body v-if="!groupStore.isItemLoading">
      <div class="flex flex-wrap items-center justify-between gap-1.5">
        <UInput
          :model-value="(table?.tableApi?.getColumn('اسم المجموعة')?.getFilterValue() as string)"
          class="max-w-sm"
          icon="i-lucide-search"
          placeholder="ابحث ..."
          @update:model-value="
            table?.tableApi?.getColumn('اسم المجموعة')?.setFilterValue($event)
          "
        />

        <div class="flex flex-wrap items-center gap-1.5">
          <DeleteModal :count="groupStore.selectedIds.length">
            <UButton
              v-if="groupStore.selectedIds.length"
              label="حذف"
              color="error"
              variant="subtle"
              icon="i-lucide-trash"
            >
              <template #trailing>
                <UKbd>{{ groupStore.selectedIds.length }}</UKbd>
              </template>
            </UButton>
          </DeleteModal>

          <UButton
            label="تصدير"
            loading-auto
            color="success"
            variant="outline"
            trailing-icon="i-lucide-file-spreadsheet"
            v-if="authStore.hasPermission('read-group')"
            @click="exportToExcel('group')"
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
        v-model:pagination="groupStore.pagination"
        class="shrink-0"
        :data="groupStore.items"
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
            :total="groupStore?.pagination?.total"
            :items-per-page="groupStore?.pagination?.pageSize"
            :default-page="groupStore?.pagination?.page"
            @update:page="(p) => groupStore.loadAllGroups(p)"
          />
        </div>
      </div>
    </template>
    <template #body v-else>
      <Loader />
    </template>
  </UDashboardPanel>
</template>
