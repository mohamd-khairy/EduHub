<script setup lang="ts">
const dashboardStore = useDashboardStore();

const props = defineProps<{
  period: Period;
  range: Range;
  stats?: any[]; // Optional prop for passing pre-fetched stats
}>();

</script>

<template>
  <UPageGrid class="lg:grid-cols-7 gap-4 sm:gap-6 lg:gap-px">
    <UPageCard
      v-for="(stat, index) in props.stats"
      :key="index"
      :icon="stat.icon"
      :title="stat.label"
      :to="stat.path"
      variant="subtle"
      :ui="{
        container: 'gap-y-1.5',
        wrapper: 'items-start',
        leading:
          'p-2.5 rounded-full bg-primary/10 ring ring-inset ring-primary/25 flex-col',
        title: 'font-normal text-muted text-2xl uppercase',
      }"
      class="lg:rounded-none hover:z-1"
    >
      <div class="flex items-center gap-2">
        <span class="text-xl font-semibold text-highlighted">
          {{ stat.count }}
        </span>
      </div>
    </UPageCard>
  </UPageGrid>
</template>
