<script setup lang="ts">
import * as z from 'zod'

const examResultStore = useExamResultStore()
const examStore = useExamStore()
const studentStore = useStudentStore()
const authStore = useAuthStore();

const open = ref(false)
const toast = useToast()

const searchExamTerm = ref('')
const searchStudentTerm = ref('')

watch(searchExamTerm, (newVal) => {
  if (newVal.length >= 3 || newVal.length < 1)
    // For example, debounce here if needed to avoid too many requests
    examStore.loadExamsForSelect(newVal)
})

watch(searchStudentTerm, (newVal) => {
  if (newVal.length >= 3 || newVal.length < 1)
    // For example, debounce here if needed to avoid too many requests
    studentStore.loadStudentsForSelect(newVal)
})

const schema = z.object({})

type Schema = z.output<typeof schema>

const state = reactive<Partial<Schema>>({
  student_id: null,
  exam_id: null,
  score: null
})

function resetState() {
  Object.assign(state, {
    student_id: null,
    exam_id: null,
    score: null
  })
}

async function onSubmit() {
  const payload = {
    score: state.score,
    exam_id: state.exam_id?.value,
    student_id: state.student_id?.value,
  }
  examResultStore.addExamResult(payload)
  toast.add({ title: 'Success', description: `درجة اختبار درجة جديد ${state.exam_id?.label} تم اضافة بنجاح`, color: 'success' })
  open.value = false
  resetState()
}

</script>

<template>
  <UModal v-model:open="open" title="اضافة اختبار درجة" description="إضافة اختبار درجة جديد" dir="rtl">
    <UButton label="إضافة اختبار درجة جديد" icon="i-lucide-plus" dir="rtl" v-if="authStore.hasPermission('create-examresult')" />

    <template #body dir="rtl">
      <UForm :schema="schema" :state="state" class="space-y-4" dir="rtl">

        <UFormField label="اسم الاختبار درجة" placeholder="اسم الاختبار درجة" name="exam_id">
          <USelectMenu required dir="rtl" v-model:search-term="searchExamTerm" v-model="state.exam_id"
            :items="examStore.examOptions" class="w-full" placeholder="اختر الاختبار درجة" :search-input="{
              placeholder: 'بحث...',
              icon: 'i-lucide-search'
            }" />
        </UFormField>

        <UFormField label="اسم الطالب" placeholder="اختر الطالب" name="student_id">
          <USelectMenu required dir="rtl" v-model:search-term="searchStudentTerm" v-model="state.student_id"
            :items="studentStore.studentOptions" class="w-full" placeholder="اختر الطالب" :search-input="{
              placeholder: 'بحث...',
              icon: 'i-lucide-search'
            }" />
        </UFormField>

        <UFormField label="درجة الطالب" placeholder="درجة الطالب" name="score">
          <UInput required v-model="state.score" max="100" type="number" placeholder="درجة الطالب"
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
