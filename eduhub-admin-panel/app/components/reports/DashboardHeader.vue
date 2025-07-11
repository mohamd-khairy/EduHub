<script setup lang="ts">
const props = defineProps<{
  title: string;
  resetSignal: boolean;
  range: any;
  group_id: any;
  student_id: any;
  isLoading: boolean;
  hasFilter: boolean;
  hasPermission: boolean;
}>();

const emit = defineEmits<{
  (e: "update:range", value: any): void;
  (e: "update:group_id", value: any): void;
  (e: "update:student_id", value: any): void;
  (e: "reset"): void;
}>();
</script>

<template>
  <UDashboardNavbar :title="title" :ui="{ right: 'gap-3' }">
    <template #leading>
      <UDashboardSidebarCollapse />
    </template>
    <template #right>
      <Communication />
    </template>
  </UDashboardNavbar>

  <UDashboardToolbar v-if="hasPermission">
    <template #left>
      <HomeDateRangePicker
        :reset-signal="resetSignal"
        :model-value="range"
        @update:model-value="(val) => emit('update:range', val)"
        class="-ms-1"
        :disabled="isLoading"
      />

      <HomeStudentSelect
        :model-value="student_id"
        @update:model-value="(val) => emit('update:student_id', val)"
        :range="range"
        :disabled="isLoading"
      />

      <HomeGroupSelect
        :model-value="group_id"
        @update:model-value="(val) => emit('update:group_id', val)"
        :range="range"
        :disabled="isLoading"
      />

      <UButton
        v-if="hasFilter"
        icon="i-lucide-x"
        color="gray"
        size="sm"
        @click="emit('reset')"
        class="hover:bg-gray-200"
      />
    </template>
  </UDashboardToolbar>
</template>
