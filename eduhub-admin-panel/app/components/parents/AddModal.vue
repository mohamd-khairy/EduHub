<script setup lang="ts">
import * as z from 'zod'

const open = ref(false)

const parentStore = useParentStore()
const authStore = useAuthStore();

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

const toast = useToast()

async function onSubmit() {
  // Add teacher to the store
  await parentStore.addParent(state)
  
  // Show success toast
  toast.add({ title: 'Success', description: `ولي امر جديد ${state.name} تم اضافة بنجاح`, color: 'success' })
  
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
    address: null,
  })
}
</script>

<template>
  <UModal v-model:open="open" title="اضافة ولي امر" description="إضافة ولي امر جديد" dir="rtl">
    <UButton label="إضافة ولي امر جديد" icon="i-lucide-plus" dir="rtl" v-if="authStore.hasPermission('create-parentmodel')" />

    <template #body dir="rtl">
      <UForm :schema="schema" :state="state" class="space-y-4" dir="rtl">
        <UFormField label="اسم الولي امر" placeholder="اسم الولي امر" name="name">
          <UInput required v-model="state.name" class="w-full" />
        </UFormField>

        <UFormField label="البريد الالكتروني" placeholder="البريد الالكتروني" name="email">
          <UInput required v-model="state.email" class="w-full" />
        </UFormField>

        <UFormField label="رقم تليفون الولي امر" placeholder="رقم تليفون الولي امر" name="phone">
          <UInput type="tel" required v-model="state.phone" class="w-full" />
        </UFormField>

        <UFormField label="عنوان الولي امر" placeholder="راتب الولي امر" name="address">
          <UInput required v-model="state.address" class="w-full" />
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
