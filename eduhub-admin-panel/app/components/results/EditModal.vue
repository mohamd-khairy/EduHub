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

const examResultStore = useExamResultStore()
const studentStore = useStudentStore()
const examStore = useExamStore()
const toast = useToast()

async function onSubmit() {
  const payload = {
    score: state.score,
    student_id: state.student_id?.value,
    exam_id: state.exam_id?.value,
  }
  examResultStore.editExamResult(payload, props.item?.id)
  toast.add({ title: 'Success', description: ` تم تعديل بنجاح`, color: 'success' })
  open.value = false
}

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

const schema = z.object({
  title: z.string().min(2, 'Too short'),
})

type Schema = z.output<typeof schema>

const state = reactive<Partial<Schema>>({
  score: null,
  exam_id: null,
  student_id: null,
})

watch(() => props.item, (val) => {
  if (!val) return
  state.score = val.score || ''
  state.exam_id = val.exam?.title || null
  state.student_id = val.student?.name || null
}, { immediate: true, deep: true })
</script>

<template>
  <UModal v-model:open="open" title="تعديل اختبار" description="تعديل اختبار " dir="rtl">
    <template #body dir="rtl">
      <UForm :schema="schema" :state="state" class="space-y-4" dir="rtl">

        <UFormField label="اسم الاختبار" placeholder="اسم الاختبار" name="exam_id">
          <USelectMenu required dir="rtl" v-model:search-term="searchExamTerm" v-model="state.exam_id"
            :items="examStore.examOptions" class="w-full" placeholder="اختر الاختبار" :search-input="{
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
          <UInput required v-model="state.score" max="100" type="number" placeholder="درجة الطالب" class="w-full" />
        </UFormField>

        <div class="flex justify-end gap-2">
          <UButton label="الغاء" color="neutral" variant="subtle" @click="open = false" />
          <UButton label="حفظ" color="primary" variant="solid" type="submit" loading-auto @click="onSubmit" />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
