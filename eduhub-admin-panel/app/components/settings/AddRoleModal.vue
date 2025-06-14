<script setup lang="ts">
import * as z from 'zod'

const open = ref(false)
const roleStore = useRoleStore()

const schema = z.object({
  name: z.string().min(2, 'Too short'),
})

type Schema = z.output<typeof schema>

const state = reactive<Partial<Schema>>({
  name: null,
})

const toast = useToast()
async function onSubmit() {
  roleStore.addRole(state)
  toast.add({ title: 'Success', description: `دور جديد ${state.name} تم اضافة بنجاح`, color: 'success' })
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
  <UModal v-model:open="open" title="اضافة دور" description="إضافة دور جديد" dir="rtl">
    <UButton variant="solid" label="إضافة دور جديد" icon="i-lucide-plus" dir="rtl" />

    <template #body dir="rtl" >
      <UForm
        dir="rtl"
        :schema="schema"
        :state="state"
        class="space-y-4"
      >
        <UFormField label="اسم الدور" placeholder="اسم الدور" name="name">
          <UInput required v-model="state.name" class="w-full" />
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
