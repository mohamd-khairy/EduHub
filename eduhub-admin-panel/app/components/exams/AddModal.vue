<script setup lang="ts">
import * as z from 'zod'

const groupStore = useGroupStore()
const examStore = useExamStore()
const authStore = useAuthStore();

const open = ref(false)
const toast = useToast()

const searchGroupTerm = ref('')

watch(searchGroupTerm, (newVal) => {
  if (newVal.length >= 3 || newVal.length < 1)
    // For example, debounce here if needed to avoid too many requests
    groupStore.loadGroupsForSelect(newVal)
})

const schema = z.object({
  title: z.string().min(2, 'Too short'),
})

type Schema = z.output<typeof schema>

const state = reactive<Partial<Schema>>({
  title: null,
  group_id: null,
  date: null,
  time: null,
  total_marks: null
})

function resetState() {
  Object.assign(state, {
    title: null,
    group_id: null,
    date: null,
    time: null,
    total_marks: null
  })
}

async function onSubmit() {
   const payload = {
    title: state.title,
    date: state.date,
    time: state.time,
    total_marks: state.total_marks,
    group_id: state.group_id?.value,
  }
  examStore.addExam(payload)
  toast.add({ title: 'Success', description: `اختبار جديد ${state.title} تم اضافة بنجاح`, color: 'success' })
  open.value = false
  resetState()
}

</script>

<template>
  <UModal v-model:open="open" title="اضافة اختبار" description="إضافة اختبار جديد" dir="rtl">
      <UButton label="إضافة اختبار جديد" icon="i-lucide-plus" dir="rtl" v-if="authStore.hasPermission('create-exam')" />

    <template #body dir="rtl">
      <UForm :schema="schema" :state="state" class="space-y-4" dir="rtl">

        <UFormField label="اسم الاختبار" placeholder="اسم الاختبار" name="title">
          <UInput required v-model="state.title" class="w-full" />
        </UFormField>

        <UFormField label="اسم المجموعة" placeholder="اختر المجموعة" name="group_id">
          <USelectMenu required dir="rtl" v-model:search-term="searchGroupTerm" v-model="state.group_id"
            :items="groupStore.groupOptions" class="w-full" placeholder="اختر المجموعة" :search-input="{
              placeholder: 'بحث...',
              icon: 'i-lucide-search'
            }" />
        </UFormField>

        <UFormField label="درجة الاختبار" placeholder="درجة الاختبار" name="total_marks">
          <UInput required v-model="state.total_marks" max="100" type="number" placeholder="درجة الاختبار"
                class="w-full" />
        </UFormField>
        
        <UFormField label="تاريخ الاختبار" placeholder="تاريخ الاختبار" name="date">
          <UInput required v-model="state.date" type="date" placeholder="اختر التاريخ"
                class="w-full" />
        </UFormField>

         <UFormField label="وقت الاختبار" placeholder="وقت الاختبار" name="time">
          <UInput required v-model="state.time" type="time" placeholder="اختر الوقت"
                class="w-full" />
        </UFormField>

        <div class="flex justify-end gap-2">
          <UButton label="الغاء" color="neutral" variant="subtle" @click="open = false" />
          <UButton label="حفظ" color="primary" variant="solid" type="submit" loading-auto @click="onSubmit" />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
