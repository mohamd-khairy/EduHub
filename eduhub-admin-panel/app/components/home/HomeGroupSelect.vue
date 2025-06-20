<script setup lang="ts">
import { eachDayOfInterval } from "date-fns";
import type { Period, Range } from "~/types";
const groupStore = useGroupStore();
const group_id = ref(null);
const periods = ref([]);
onMounted(async () => {
  await groupStore.loadGroupsForSelect();
  periods.value = groupStore.groupOptions;
});

// Ensure the model value is always a valid period
watch(periods, () => {});
</script>

<template>
  <USelect  placeholder="اختر المجموعة" v-model="group_id" :items="periods" variant="ghost" class="w-34" />
</template>
