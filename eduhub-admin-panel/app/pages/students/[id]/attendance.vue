<script setup lang="ts">
import { ref, onMounted } from "vue";
import * as z from "zod";
import type { TabsItem } from "@nuxt/ui";

const props = defineProps<{
  student: any; // use `any` or define a type if available
}>();

const items = ref<TabsItem[]>([]);
const activeGroupTab = ref<string | number | null>(null); // model for selected tab
const activeScheduleTab = ref<string | number | null>(null); // model for selected tab

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

    if (items.value[0]?.schedules && items.value[0].schedules.length > 0) {
      // Set the first schedule tab as active
      activeScheduleTab.value = items.value[0].schedules[0].value;
    }
  }
});

</script>

<template>
  <UTabs
    v-model="activeGroupTab"
    :items="items"
    class="gap-4 w-full"
    :ui="{ trigger: 'grow' }"
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
                <th class="border px-4 py-2 text-gray-800">اليوم</th>
                <th class="border px-4 py-2 text-gray-800">الوقت</th>
                <th class="border px-4 py-2 text-gray-800">الحضور</th>
                <th class="border px-4 py-2 text-gray-800">ملاحظات</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              <tr v-for="attendance in item.attendances" :key="attendance.id">
                <td class="border px-4 py-2">{{ attendance.date }}</td>
                <td class="border px-4 py-2">
                  {{
                    new Date(attendance.date).toLocaleDateString("ar-EG", {
                      weekday: "long",
                    })
                  }}
                </td>
                <td class="border px-4 py-2">
                  {{
                    new Date(attendance.created_at).toLocaleTimeString(
                      "ar-EG",
                      {
                        hour: "2-digit",
                        minute: "2-digit",
                      }
                    )
                  }}
                </td>
                <td class="border px-4 py-2">{{ attendance.status }}</td>
                <td class="border px-4 py-2">{{ attendance.note }}</td>
              </tr>
            </tbody>
          </table>
        </template>
      </UTabs>
      <!-- <template #content="{ item }"> -->
      <!-- <UTable :data="item?.attendance" class="flex-1" />
           
          -->

      <!-- </template> -->
      <!-- </UTabs> -->
    </template>
  </UTabs>
</template>
