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


</script>

<template>
  <UTable
    ref="table"
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
