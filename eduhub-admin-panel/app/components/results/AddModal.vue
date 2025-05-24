<script setup lang="ts">
import * as z from 'zod'
import type { FormSubmitEvent } from '@nuxt/ui'

const schema = z.object({
  name: z.string().min(2, 'Too short'),
  email: z.string().email('Invalid email')
})
const open = ref(false)

type Schema = z.output<typeof schema>

const state = reactive<Partial<Schema>>({
  name: undefined,
  email: undefined
})

const toast = useToast()
async function onSubmit(event: FormSubmitEvent<Schema>) {
  toast.add({ title: 'Success', description: `درجة جديد ${event.data.name} تم اضافتة بنجاح`, color: 'success' })
  open.value = false
}
</script>

<template>
  <UModal v-model:open="open" title="اضافة درجة" description="إضافة درجة جديد">
    <UButton label="إضافة درجة جديد" icon="i-lucide-plus" />

    <template #body>
      <UForm
        :schema="schema"
        :state="state"
        class="space-y-4"
        @submit="onSubmit"
      >
        <UFormField label="Name" placeholder="John Doe" name="name">
          <UInput v-model="state.name" class="w-full" />
        </UFormField>
        <UFormField label="Email" placeholder="john.doe@example.com" name="email">
          <UInput v-model="state.email" class="w-full" />
        </UFormField>
        <div class="flex justify-end gap-2">
          <UButton
            label="Cancel"
            color="neutral"
            variant="subtle"
            @click="open = false"
          />
          <UButton
            label="Create"
            color="primary"
            variant="solid"
            type="submit"
          />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
