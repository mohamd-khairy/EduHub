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

const teacherStore = useTeacherStore()

const toast = useToast()

async function onSubmit() {
  teacherStore.editTeacher(state, props.item?.id)
  toast.add({ title: 'Success', description: `مدرس  ${state.name} تم تعديل بنجاح`, color: 'success' })
  open.value = false
}

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

watch(() => props.item, (val) => {
  if (!val) return
  state.name = val.name || ''
  state.email = val.email || null
  state.phone = val.phone || null
  state.specialization = val.specialization || null
  state.salary_amount = val.salary_amount || null
}, { immediate: true, deep: true })

</script>

<template>
  <UModal v-model:open="open" title="تعديل مجموعة" description="تعديل مدرس جديد" dir="rtl">
    <template #body dir="rtl">
      <UForm :schema="schema" :state="state" class="space-y-4" dir="rtl">

        <UFormField label="اسم المدرس" placeholder="اسم المدرس" name="name">
          <UInput required v-model="state.name" class="w-full" />
        </UFormField>

        <UFormField label="البريد الالكتروني" placeholder="البريد الالكتروني" name="email">
          <UInput required v-model="state.email" class="w-full" />
        </UFormField>

        <UFormField label="رقم تليفون المدرس" placeholder="رقم تليفون المدرس" name="phone">
          <UInput type="tle" required v-model="state.phone" class="w-full" />
        </UFormField>

        <UFormField label="تخصص المدرس" placeholder="تخصص المدرس" name="specialization">
          <UInput required v-model="state.specialization" class="w-full" />
        </UFormField>

        <UFormField label="راتب المدرس" placeholder="راتب المدرس" name="salary_amount">
          <UInput type="number" required v-model="state.salary_amount" class="w-full" />
        </UFormField>

        <div class="flex justify-end gap-2">
          <UButton label="الغاء" color="neutral" variant="subtle" @click="open = false" />
          <UButton label="حفظ" color="primary" variant="solid" type="submit" loading-auto @click="onSubmit" />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
