<script setup lang="ts">
import * as z from 'zod'


const props = defineProps({
  open: Boolean,
  item: Object
})

const emit = defineEmits(['update:open'])

watch(() => props.open, (val) => {
  open.value = val
})

const open = ref(false)

watch(open, (val) => {
  emit('update:open', val)
})


const courseStore = useCourseStore()

const schema = z.object({
  name: z.string().min(2, 'Too short'),
  email: z.string().email('Invalid email')
})

type Schema = z.output<typeof schema>


const toast = useToast()

async function onSubmit() {
  courseStore.editCourse(state, props.item?.id)
  toast.add({ title: 'Success', description: `كورس  ${state.name} تم تعديل بنجاح`, color: 'success' })
  open.value = false
}

const state = reactive<Partial<Schema>>({
  name: null,
  description: null
})

watch(() => props.item, (val) => {
  if (!val) return
  state.name = val.name || ''
  state.description = val.description || null
}, { immediate: true, deep: true })
</script>

<template>
  <UModal v-model:open="open" title="تعديل كورس" description="تعديل كورس " dir="rtl">
    <template #body dir="rtl">
      <UForm :schema="schema" :state="state" class="space-y-4" dir="rtl">

        <UFormField label="اسم الكورس" placeholder="اسم الكورس" name="name">
          <UInput required v-model="state.name" class="w-full" />
        </UFormField>

     
        <UFormField label="تفاصيل الكورس" placeholder="تفاصيل كورس" name="name">
          <UTextarea  required v-model="state.description" class="w-full" />
        </UFormField>


        <div class="flex justify-end gap-2">
          <UButton label="الغاء" color="neutral" variant="subtle" @click="open = false" />
          <UButton label="حفظ" color="primary" variant="solid" loading-auto type="submit" @click="onSubmit" />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
