<script setup lang="ts">
import { ref, onMounted } from "vue";

const props = defineProps<{
  student: any; // use `any` or define a type if available
}>();

const items = ref([]);
const activeGroupTab = ref(null); // model for selected tab
const activeScheduleTab = ref(null); // model for selected tab

onMounted(() => {
  if (props.student?.groups && Array.isArray(props.student.groups)) {

    items.value =  props.student?.groups;    

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

function getStatusColor(status: string): string {
  switch (status) {
    case "حضر":
      return "success";
    case "غائب":
      return "error";
    case "متأخر":
      return "warning";
    default:
      return "neutral";
  }
}
</script>

<template>
  <UPageCard
    title="الحضور والغياب الخاص بالطالب"
    :description="student.name"
    variant="naked"
    orientation="horizontal"
    class="mt-2"
  >
  </UPageCard>
  <UTabs
    v-model="activeGroupTab"
    :items="items"
  >
    <template #content="{ item }">
      <UTabs
        v-model="activeScheduleTab"
        variant="link"
        :items="item.schedules"
        class="gap-4 w-full"
        :ui="{ trigger: 'grow' }"
      >
        <template #content="{ item }">
          <table class="w-full text-center border">
            <thead class="bg-gray-200">
              <tr>
                <th class="border px-4 py-2 text-gray-800">التاريخ</th>
                <th class="border px-4 py-2 text-gray-800">الوقت</th>
                <th class="border px-4 py-2 text-gray-800">الحضور</th>
                <th class="border px-4 py-2 text-gray-800">ملاحظات</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              <tr v-for="attendance in item.attendances" :key="attendance.id">
                <td class="border px-4 py-2">
                  {{
                    new Date(attendance.created_at).toLocaleString("ar-EG", {
                      weekday: "long",
                      year: "numeric",
                      month: "long",
                      day: "numeric",
                    })
                  }}
                </td>
                <td class="border px-4 py-2">
                  {{
                    new Date(attendance.created_at).toLocaleString("ar-EG", {
                      hour: "2-digit",
                      minute: "2-digit",
                    })
                  }}
                </td>
                <td class="border px-4 py-2">
                  <UBadge :color="getStatusColor(attendance.status)">{{
                    attendance.status
                  }}</UBadge>
                </td>
                <td class="border px-4 py-2">{{ attendance.note }}</td>
              </tr>
            </tbody>
          </table>
        </template>
      </UTabs>
    </template>
  </UTabs>
</template>
