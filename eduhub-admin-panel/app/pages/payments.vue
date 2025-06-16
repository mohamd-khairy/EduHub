<script setup lang="ts">
definePageMeta({
  permission: "read-payment",
});
import type { TableColumn } from "@nuxt/ui";
import { upperFirst } from "scule";
import AddModal from "~/components/payments/AddModal.vue";
import DeleteModal from "~/components/payments/DeleteModal.vue";
import EditModal from "~/components/payments/EditModal.vue";

const paymentStore = usePaymentStore();
const studentStore = useStudentStore();

const UButton = resolveComponent("UButton");
const UBadge = resolveComponent("UBadge");
const UDropdownMenu = resolveComponent("UDropdownMenu");
const UCheckbox = resolveComponent("UCheckbox");
const toast = useToast();
const table = useTemplateRef("table");

const columnFilters = ref([
  {
    id: "حالة الدفع",
    value: "",
  },
]);

const columnVisibility = ref();

onMounted(() => {
  paymentStore.loadAllPayments();
  studentStore.loadStudentsForSelect();
});

function getRowItems(row: any) {
  return [
    { type: "label", label: "الاجراءات" },
    { label: "مشاهدة الفاتورة", icon: "i-lucide-wallet" },
    { type: "separator" },
    {
      label: "تعديل الدفع",
      icon: "i-lucide-edit",
      color: "primary",
      onSelect() {
        paymentStore.editItem = row.original;
        paymentStore.editModalOpen = true;
      },
    },
    {
      label: "حذف الدفع",
      icon: "i-lucide-trash",
      color: "error",
      onSelect() {
        paymentStore.addId(row.original.id);
        paymentStore.deleteModalOpen = true;
      },
    },
  ];
}

const getColorByStatus = (status) => {
  return {
    paid: "success",
    cancelled: "error",
    pending: "warning",
  }[status];
};


const getStatus = (status) => {
  return {
    paid: "تم الدفع",
    cancelled: "لم يتم الدفع",
    pending: "في انتظار الدفع",
  }[status];
};

const columns: TableColumn[] = [
  {
    id: "اختار",
    header: ({ table }) =>
      h(UCheckbox, {
        modelValue:
          paymentStore.selectedIds.length > 0 &&
          paymentStore.selectedIds.length ===
            table.getFilteredRowModel().rows.length
            ? true
            : paymentStore.selectedIds?.length > 0
            ? "indeterminate"
            : false,
        "onUpdate:modelValue": (value: boolean | "indeterminate") => {
          if (value) {
            // Select all visible rows
            table.getFilteredRowModel().rows.forEach((r) => {
              paymentStore.toggleId(r.original.id);
            });
          } else {
            // Deselect all visible rows
            table.getFilteredRowModel().rows.forEach((r) => {
              paymentStore.removeId(r.original.id);
            });
          }
        },
        ariaLabel: "Select all",
      }),
    cell: ({ row }) =>
      h(UCheckbox, {
        modelValue: paymentStore.selectedIds.includes(row.original.id),
        "onUpdate:modelValue": (value: boolean | "indeterminate") => {
          if (value) {
            paymentStore.addId(row.original.id);
          } else {
            paymentStore.removeId(row.original.id);
          }
        },
        ariaLabel: "Select row",
      }),
  },
  {
    accessorKey: "id",
    id: "الرقم",
    header: "الرقم",
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
    accessorKey: "student",
    id: "اسم الطالب",
    cell: ({ row }) => {
      return h(() => row.original.student?.name);
    },
    header: ({ column }) => {
      const isSorted = column.getIsSorted();

      return h(UButton, {
        color: "neutral",
        variant: "ghost",
        label: "اسم الطالب",
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
    accessorKey: "amount",
    id: "المبلغ المدفوع",
    header: "المبلغ المدفوع",
    cell: ({ row }) => {
      const color = "warning";

      return h(() => row.original.amount);
    },
  },
  {
    accessorKey: "method",
    id: "طريقة الدفع",
    header: "طريقة الدفع",
    cell: ({ row }) => {
      const color = "warning";

      return h(
        () => row.original.method
      );
    },
  },
  {
    accessorKey: "status",
    id: "حالة الدفع",
    header: "حالة الدفع",
    cell: ({ row }) => {
      const state = row.original.status;
      const color = getColorByStatus(state);
      return h(
        UBadge,
        {
          color: color,
          class: "capitalize",
        },
        () => getStatus(state)
      );
    },
  },
  {
    accessorKey: "payment_date",
    id: "تاريخ الدفع",
    header: "تاريخ الدفع",
    filterFn: "equals",
    cell: ({ row }) => {
      const color = "success";

      return h(
        UBadge,
        { class: "capitalize", variant: "subtle", color },
        () => row.original.payment_date
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
      <UDashboardNavbar title="المدفوعات">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <AddModal />

          <DeleteModal
            :count="paymentStore.selectedIds.length"
            v-model:open="paymentStore.deleteModalOpen"
          />

          <EditModal
            :item="paymentStore.editItem"
            v-model:open="paymentStore.editModalOpen"
          />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="flex flex-wrap items-center justify-between gap-1.5">
        <UInput
          :model-value="(table?.tableApi?.getColumn('حالة الدفع')?.getFilterValue() as string)"
          class="max-w-sm"
          icon="i-lucide-search"
          placeholder="ابحث ..."
          @update:model-value="
            table?.tableApi?.getColumn('حالة الدفع')?.setFilterValue($event)
          "
        />

        <div class="flex flex-wrap items-center gap-1.5">
          <DeleteModal :count="paymentStore.selectedIds.length">
            <UButton
              v-if="paymentStore.selectedIds.length"
              label="حذف"
              color="error"
              variant="subtle"
              icon="i-lucide-trash"
            >
              <template #trailing>
                <UKbd>
                  {{ paymentStore.selectedIds.length }}
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
        v-model:pagination="paymentStore.pagination"
        class="shrink-0"
        :data="paymentStore.items"
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
            :total="paymentStore.pagination?.total"
            :items-per-page="paymentStore.pagination?.pageSize"
            :default-page="paymentStore.pagination?.page"
            @update:page="(p) => paymentStore.loadAllPayments(p)"
          />
        </div>
      </div>
    </template>
  </UDashboardPanel>
</template>
