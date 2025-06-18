<script setup lang="ts">
import * as z from 'zod'

const open = ref(false)
const courseStore = useCourseStore()
const authStore = useAuthStore();

const schema = z.object({
  name: z.string().min(2, 'Too short'),
  description: z.string().min(2, 'Too short'),
})

type Schema = z.output<typeof schema>

const state = reactive<Partial<Schema>>({
  name: null,
  description: null
})

const toast = useToast()
async function onSubmit() {
  courseStore.addCourse(state)
  toast.add({ title: 'Success', description: `كورس جديد ${state.name} تم اضافة بنجاح`, color: 'success' })
  open.value = false
  resetState()
}
function resetState() {
  Object.assign(state, {
    name: null,
    description: null
  })
}
</script>

<template>
  <UModal v-model:open="open" title="اضافة كورس" description="إضافة كورس جديد" dir="rtl">
    <UButton label="إضافة كورس جديد" icon="i-lucide-plus" dir="rtl" v-if="authStore.hasPermission('create-course')" />

    <template #body dir="rtl" >
      <UForm
        dir="rtl"
        :schema="schema"
        :state="state"
        class="space-y-4"
      >
        <UFormField label="اسم الكورس" placeholder="اسم الكورس" name="name">
          <UInput required v-model="state.name" class="w-full" />
        </UFormField>

        <UFormField label="تفاصيل الكورس" placeholder="تفاصيل الكورس" name="name">
          <UTextarea  required v-model="state.description" class="w-full" />
        </UFormField>

        <div class="flex justify-end gap-2">
          <UButton
            label="الغاء"
            color="neutral"
            variant="subtle"
            @click="open = false"
          />
          <UButton
            label="حفظ"
            color="primary"
            variant="solid"
            type="submit"
            loading-auto
            @click="onSubmit"
          />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
