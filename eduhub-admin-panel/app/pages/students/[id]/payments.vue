<script setup lang="ts">
import { ref, onMounted } from "vue";
import * as z from "zod";
import type { TabsItem } from "@nuxt/ui";
import { h, resolveComponent } from "vue";
import type { TableColumn } from "@nuxt/ui";
const UBadge = resolveComponent("UBadge");
const UButton = resolveComponent("UButton");

const props = defineProps<{
  student: any; // use `any` or define a type if available
}>();

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
  <UPageCard
    title="المدفوعات الخاصة بالطالب"
    :description="student.name"
    variant="naked"
    orientation="horizontal"
  >
  </UPageCard>
  <UTable :data="props.student.payments" :columns="columns" class="w-full"></UTable>
</template>
