<script setup lang="ts">
import { useGroupStore } from '~/stores/groupStore'

const groupStore = useGroupStore()

const props = withDefaults(defineProps<{
  count?: number,
  ids?: number[]
}>(), {
  count: 0
})

const open = ref(false)

async function onSubmit() {
  await new Promise(resolve => setTimeout(resolve, 1000))
  // emit('confirm-delete', props.ids)

  await groupStore.deleteSelectedGroups(props.ids)

  open.value = false
}
</script>

<template>
  <UModal v-model:open="open" :title="`حذف ${count} مجموعة`"
    :description="`هل أنت متأكد؟ هذا الإجراء لا يمكن التراجع عنه.`">
    <slot />

    <template #body>
      <div class="flex justify-end gap-2">
        <UButton label="Cancel" color="neutral" variant="subtle" @click="open = false" />
        <UButton label="Delete" color="error" variant="solid" loading-auto @click="onSubmit" />
      </div>
    </template>
  </UModal>
</template>
