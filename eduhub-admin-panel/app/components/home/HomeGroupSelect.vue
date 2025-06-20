<script setup lang="ts">
const groupStore = useGroupStore();
const group_id = ref(null);
const groups = ref([]);
onMounted(async () => {
  await groupStore.loadGroupsForSelect();
  groups.value = groupStore.groupOptions;
});
</script>

<template>
  <USelect
    placeholder="اختر المجموعة"
    v-model="group_id"
    :items="groups"
    variant="ghost"
    class="w-34"
  />
</template>
