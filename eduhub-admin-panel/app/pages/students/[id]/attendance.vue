<script setup lang="ts">
import { ref, onMounted } from "vue";
definePageMeta({
  permission: "read-student-attendance",
});
const props = defineProps<{
  student: any; // use `any` or define a type if available
}>();

const items = ref([]);
const activeGroupTab = ref(null); // model for selected tab
const activeScheduleTab = ref(null); // model for selected tab
const UBadge = resolveComponent("UBadge");

onMounted(() => {
  if (props.student?.groups && Array.isArray(props.student.groups)) {
    items.value = props.student?.groups;

    if (items.value.length > 0) {
      activeGroupTab.value = items.value[0]?.value;
    }
  }
});

watch(activeGroupTab, async (id) => {
  const obj = items.value.find((item) => item.id == id);
  if (obj?.schedules.length > 0) {
    activeScheduleTab.value = obj?.schedules[0]?.value;
  }
});

const statusColors = {
  حضر: "success",
  غائب: "error",
  متأخر: "warning",
};

// Table Columns
const columns = [
  {
    accessorKey: "date",
    id: "date",
    header: " التاريخ",
    cell: ({ row }) =>
      h(() =>
        new Date(row.original.created_at).toLocaleString("ar-EG", {
          weekday: "long",
          year: "numeric",
          month: "long",
          day: "numeric",
        })
      ),
  },
  {
    accessorKey: "time",
    id: "time",
    header: " الوقت",
    cell: ({ row }) =>
      h(() =>
        new Date(row.original.created_at).toLocaleString("ar-EG", {
          hour: "2-digit",
          minute: "2-digit",
        })
      ),
  },
  {
    accessorKey: "status",
    id: "status",
    header: "حالة الحضور",
    cell: ({ row }) =>
      h(
        UBadge,
        {
          class: "capitalize",
          variant: "subtle",
          color: statusColors[row.original.status],
        },
        () => row.original.status
      ),
  },
  {
    accessorKey: "note",
    id: "note",
    header: "الملاحظات",
  },
];
</script>

<template>
  <UTabs v-model="activeGroupTab" :items="items" size="xl">
    <template #content="{ item }">
      <UTabs
        v-model="activeScheduleTab"
        variant="link"
        :items="item.schedules"
        class="gap-4 w-full"
        :ui="{ trigger: 'grow' }"
        size="xl"
      >
        <template #content="{ item }">
          <div class="border rounded-lg overflow-hidden text-lg">
            <UTable :columns="columns" :data="item.attendances" class="w-full">
            </UTable>
          </div>
        </template>
      </UTabs>
    </template>
  </UTabs>
</template>
