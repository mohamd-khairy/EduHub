<script setup lang="ts">
withDefaults(defineProps<{
  count?: number
}>(), {
  count: 0
})

const teacherStore = useTeacherStore()

const open = ref(false)

async function onSubmit() {
  await new Promise(resolve => setTimeout(resolve, 1000))

    await teacherStore.deleteSelectedTeachers()

  open.value = false

    if (!open.value)
    toast.add({
      title: 'حذف المدرس',
      description: 'تم حذف المدرس بنجاح'
    })
}
</script>

<template>
<UModal v-model:open="open" :title="`حذف  ${count}  المدرس`"
    :description="`هل أنت متأكد؟ هذا الإجراء لا يمكن التراجع عنه.`">
    <slot />

    <template #body>
      <div class="flex justify-end gap-2">
        <UButton
          label="الغاء"
          color="neutral"
          variant="subtle"
          @click="open = false"
        />
        <UButton
          label="حذف"
          color="error"
          variant="solid"
          loading-auto
          @click="onSubmit"
        />
      </div>
    </template>
  </UModal>
</template>
