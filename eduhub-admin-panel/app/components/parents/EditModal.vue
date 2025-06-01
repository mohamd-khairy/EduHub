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

const parentStore = useParentStore()

const toast = useToast()

async function onSubmit() {
  parentStore.editParent(state, props.item?.id)
  toast.add({ title: 'Success', description: `ولي امر  ${state.name} تم تعديل بنجاح`, color: 'success' })
  open.value = false
}

const schema = z.object({
  name: z.string().min(2, 'Too short'),
  phone: z.string(),
  address: z.string().min(2, 'Too short'),
  email: z.string().email('Invalid email')
})

type Schema = z.output<typeof schema>

const state = reactive<Partial<Schema>>({
  name: null,
  phone: null,
  email: null,
  address: null,
})

watch(() => props.item, (val) => {
  if (!val) return
  state.name = val.name || ''
  state.email = val.email || null
  state.phone = val.phone || null
  state.address = val.address || null
}, { immediate: true, deep: true })

</script>

<template>
  <UModal v-model:open="open" title="تعديل مجموعة" description="تعديل ولي امر جديد" dir="rtl">
    <template #body dir="rtl">
      <UForm :schema="schema" :state="state" class="space-y-4" dir="rtl">

        <UFormField label="اسم الولي امر" placeholder="اسم الولي امر" name="name">
          <UInput required v-model="state.name" class="w-full" />
        </UFormField>

        <UFormField label="البريد الالكتروني" placeholder="البريد الالكتروني" name="email">
          <UInput required v-model="state.email" class="w-full" />
        </UFormField>

        <UFormField label="رقم تليفون الولي امر" placeholder="رقم تليفون الولي امر" name="phone">
          <UInput type="tle" required v-model="state.phone" class="w-full" />
        </UFormField>

        <UFormField label="عنوان الولي امر" placeholder="عنوان الولي امر" name="address">
          <UInput required v-model="state.address" class="w-full" />
        </UFormField>

        <div class="flex justify-end gap-2">
          <UButton label="الغاء" color="neutral" variant="subtle" @click="open = false" />
          <UButton label="حفظ" color="primary" variant="solid" type="submit" loading-auto @click="onSubmit" />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
