<script setup lang="ts">
import * as z from 'zod'

const open = ref(false)

const teacherStore = useTeacherStore()
const authStore = useAuthStore();

const schema = z.object({
  name: z.string().min(2, 'Too short'),
  phone: z.string(),
  specialization: z.string().min(2, 'Too short'),
  email: z.string().email('Invalid email')
})

type Schema = z.output<typeof schema>

const state = reactive<Partial<Schema>>({
  name: null,
  phone: null,
  email: null,
  specialization: null,
  salary_amount: null
})

const toast = useToast()

async function onSubmit() {
  // Add teacher to the store
  await teacherStore.addTeacher(state)
  
  // Show success toast
  toast.add({ title: 'Success', description: `مدرس جديد ${state.name} تم اضافة بنجاح`, color: 'success' })
  
  // Reset state after submission
  resetState()
  // Close the modal
  open.value = false
}

function resetState() {
  Object.assign(state, {
    name: null,
    phone: null,
    email: null,
    specialization: null,
    salary_amount: null
  })
}
</script>

<template>
  <UModal v-model:open="open" title="اضافة مدرس" description="إضافة مدرس جديد" dir="rtl">
    <UButton label="إضافة مدرس جديد" icon="i-lucide-plus" dir="rtl" v-if="authStore.hasPermission('create-teacher')" />

    <template #body dir="rtl">
      <UForm :schema="schema" :state="state" class="space-y-4" dir="rtl">
        <UFormField label="اسم المدرس" placeholder="اسم المدرس" name="name">
          <UInput required v-model="state.name" class="w-full" />
        </UFormField>

        <UFormField label="البريد الالكتروني" placeholder="البريد الالكتروني" name="email">
          <UInput required v-model="state.email" class="w-full" />
        </UFormField>

        <UFormField label="رقم تليفون المدرس" placeholder="رقم تليفون المدرس" name="phone">
          <UInput type="tel" required v-model="state.phone" class="w-full" />
        </UFormField>

        <UFormField label="تخصص المدرس" placeholder="تخصص المدرس" name="specialization">
          <UInput required v-model="state.specialization" class="w-full" />
        </UFormField>

        <UFormField label="راتب المدرس" placeholder="راتب المدرس" name="salary_amount">
          <UInput type="number" required v-model="state.salary_amount" class="w-full" />
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
