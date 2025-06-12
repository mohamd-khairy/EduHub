<script setup lang="ts">
import { h, resolveComponent } from "vue";
import type { TableColumn } from "@nuxt/ui";
const UBadge = resolveComponent("UBadge");
const UButton = resolveComponent("UButton");

const auditStore = useAuditStore();

onMounted(async () => {
  await auditStore.loadAllAudits();
});

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

const columnFilters = ref([{
  id: 'اسم الكورس',
  value: ''
}])

const columnVisibility = ref()

const columns: TableColumn<Payment>[] = [
  {
    accessorKey: "id",
    header: "#",
    cell: ({ row }) => `#${row.getValue("id")}`,
  },
  {
    accessorKey: "created_at",
    header: "تاريخ الدفع",
    cell: ({ row }) => {
      return new Date(row.getValue("created_at")).toLocaleString("en-US", {
        day: "numeric",
        month: "short",
        hour: "2-digit",
        minute: "2-digit",
        hour12: false,
      });
    },
  },
  {
    accessorKey: "status",
    header: "حالة الدفع",
    cell: ({ row }) => {
      const state = row.getValue("status");
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
    accessorKey: "method",
    header: "طريقة الدفع",
  },
  {
    accessorKey: "amount",
    header: "قيمة الدفع",
    header: () => h("div", { class: "text-right" }, "قيمة الدفع"),
    cell: ({ row }) => {
      const amount = Number.parseFloat(row.getValue("amount"));

      const formatted = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "EGP",
      }).format(amount);

      return h("div", { class: "text-right font-medium" }, formatted);
    },
  },
  {
    id: "note",
    accessorKey: "note",
    header: "الملاحظات",
  },
  {
    id: "actions",
    accessorKey: "actions",
    header: "الاجراءات",
    cell: ({ row }) => {
      return h(
        UButton,
        {
          color: "neutral",
          variant: "soft",
          icon: "i-lucide-dollar-sign",
          onClick: () => {
            // Handle delete action
            console.log("Delete payment", row.getValue("id"));
          },
        },
        () => "الفاتورة"
      );
    },
  },
];
</script>

<template>
  <UTable
    ref="table"
    v-model:column-filters="columnFilters"
    v-model:column-visibility="columnVisibility"
    v-model:pagination="auditStore.pagination"
    class="shrink-0"
    :data="auditStore.items"
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
        :total="auditStore.pagination?.total"
        :items-per-page="auditStore.pagination?.pageSize"
        :default-page="auditStore.pagination?.page"
        @update:page="(p) => auditStore.loadAllAudits(p)"
      />
    </div>
  </div>
</template>
