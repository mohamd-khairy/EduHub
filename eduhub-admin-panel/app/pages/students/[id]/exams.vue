<script setup lang="ts">
import { ref, onMounted } from "vue";
import * as z from "zod";
import type { TabsItem } from "@nuxt/ui";
import { h, resolveComponent } from "vue";
import type { TableColumn } from "@nuxt/ui";
const UBadge = resolveComponent("UBadge");
definePageMeta({
  permission: "read-student-exam",
});
const props = defineProps<{
  student: any; // use `any` or define a type if available
}>();

const items = ref<TabsItem[]>([]);
const activeGroupTab = ref<string | number | null>(null); // model for selected tab

onMounted(() => {
  if (props.student?.groups && Array.isArray(props.student.groups)) {
    // transform groups to include label/value for UTabs
    const mappedGroups = props.student?.groups.map((group) => ({
      ...group,
      label: group.name, // used for UTabs display
      value: group.id, // used for UTabs v-model
    }));

    items.value = mappedGroups;

    // ✅ Set the first tab as active
    if (items.value.length > 0) {
      activeGroupTab.value = items.value[0].value;
    }
  }
});

const getColorByStatus = (status: PaymentStatus) => {
  return {
    paid: "success",
    failed: "error",
    refunded: "neutral",
  }[status];
};

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
    id: "title",
    header: "اسم الاختبار",
    accessorKey: "title",
    cell: ({ row }) => {
      return h(
        "div",
        { class: "text-right font-medium" },
        row.getValue("title")
      );
    },
  },
  {
    id: "date",
    header: "تاريخ الاختبار",
    accessorKey: "date",
    cell: ({ row }) => {
      return h(
        "div",
        { class: "text-right font-medium" },
        row.getValue("date")
      );
    },
  },
  {
    id: "time",
    header: "وقت الاختبار",
    accessorKey: "time",
    cell: ({ row }) => {
      return h(
        "div",
        { class: "text-right font-medium" },
        row.getValue("time")
      );
    },
  },
  {
    id: "total_marks",
    header: "درجة الاختبار",
    accessorKey: "total_marks",
    cell: ({ row }) => {
      return h(
        "div",
        { class: "text-right font-medium" },
        row.getValue("total_marks")
      );
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

const UButton = resolveComponent("UButton");

const expanded = ref({ 0: false });
</script>

<template>
  <UTabs
    v-model="activeGroupTab"
    :items="items"
    class="gap-4 w-full"
    :ui="{ trigger: 'grow' }"
    size="xl"
  >
    <template #content="{ item }">
      <UTable
        v-model:expanded="expanded"
        :data="item.exams"
        :columns="columns"
        :ui="{ tr: 'data-[expanded=true]:bg-elevated/50' }"
        class="flex-1"
      >
        <template #expanded="{ row }">
          <table class="w-full table-auto border mt-2 text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="border px-2 py-1">التاريخ والوقت</th>
                <th class="border px-2 py-1">الدرجة</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(result, index) in row.original.results" :key="index">
                <td class="border px-2 py-1">
                  {{
                    new Date(result.created_at).toLocaleString("ar-EG", {
                      weekday: "long", // optional (like: الأحد)
                      year: "numeric",
                      month: "long",
                      day: "numeric",
                      hour: "2-digit",
                      minute: "2-digit",
                    })
                  }}
                </td>
                <td class="border px-2 py-1">
                  <UBadge>{{ result.score }}</UBadge>
                </td>
              </tr>
            </tbody>
          </table>
        </template>
      </UTable>
    </template>
  </UTabs>
</template>
