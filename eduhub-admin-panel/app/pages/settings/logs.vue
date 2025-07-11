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
    created: "success",
    deleted: "error",
    updated: "warning",
  }[status];
};

const expanded = ref({ 0: false });

const columns: TableColumn<Payment>[] = [
  {
    id: "id",
    header: "#",
    accessorKey: "id",
    cell: ({ row }) => {
      return h("div", { class: "text-right font-medium" }, row.getValue("id"));
    },
  },
  {
    id: "user_type",
    header: " نوع المستخدم",
    accessorKey: "user_type",
    cell: ({ row }) => {
      return h(
        "div",
        { class: "text-right font-medium" },
        row.getValue("user_type")
      );
    },
  },
  {
    id: "user_id",
    header: " رقم المستخدم ",
    accessorKey: "user_id",
    cell: ({ row }) => {
      return h(
        "div",
        { class: "text-right font-medium" },
        row.getValue("user_id")
      );
    },
  },
  {
    id: "event",
    header: " نوع الحدث",
    accessorKey: "event",

    cell: ({ row }) =>
      h(
        UBadge,
        {
          class: "capitalize",
          variant: "subtle",
          color: getColorByStatus(row.original.event),
        },
        () => row.getValue("event")
      ),
  },
  {
    id: "auditable_type",
    header: "الموديل",
    accessorKey: "auditable_type",
    cell: ({ row }) => {
      return h(
        "div",
        { class: "text-right font-medium" },
        row.getValue("auditable_type")
      );
    },
  },
  {
    id: "auditable_id",
    header: "رقم الموديل",
    accessorKey: "auditable_id",
    cell: ({ row }) => {
      return h(
        "div",
        { class: "text-right font-medium" },
        row.getValue("auditable_id")
      );
    },
  },
  {
    id: "url",
    header: "الرابط",
    accessorKey: "url",
    cell: ({ row }) => {
      return h("div", { class: "text-right font-medium" }, row.getValue("url"));
    },
  },
  {
    id: "expand",
    cell: ({ row }) =>
      h(UButton, {
        color: "neutral",
        variant: "ghost",
        icon: "i-lucide-chevron-down",
        square: true,
        "aria-label": "Expand",
        ui: {
          leadingIcon: [
            "transition-transform",
            row.getIsExpanded() ? "duration-200 rotate-180" : "",
          ],
        },
        onClick: () => row.toggleExpanded(),
      }),
  },
];
</script>

<template>
  <template v-if="auditStore.items.length > 0">
    <UTable
      v-model:expanded="expanded"
      v-model:pagination="auditStore.pagination"
      :columns="columns"
      :data="auditStore.items"
      :ui="{
        tr: 'data-[expanded=true]:bg-elevated/50',
      }"
      class="flex-1"
    >
      <template #expanded="{ row }">
        <table class="w-full table-auto border mt-2 text-sm">
          <thead class="bg-gray-100">
            <tr class="dark:bg-elevated/100">
              <th class="border px-2 py-1">النوع</th>
              <th class="border px-2 py-1">القيم</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="border px-2 py-1">القيم القديمة</td>
              <td class="border px-2 py-1">
                {{ row.original.old_values }}
              </td>
            </tr>
            <tr>
              <td class="border px-2 py-1">القيم الحديثة</td>

              <td class="border px-2 py-1">
                {{ row.original.new_values }}
              </td>
            </tr>
          </tbody>
        </table>
      </template>
    </UTable>

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
  <template v-else>
    <Loader />
  </template>
</template>
