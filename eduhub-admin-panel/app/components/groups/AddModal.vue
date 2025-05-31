<script setup lang="ts">
import * as z from 'zod'

const groupStore = useGroupStore()
const courseStore = useCourseStore()
const teacherStore = useTeacherStore()

const schema = z.object({
  name: z.string().min(2, 'Too short'),
})
const open = ref(false)

type Schema = z.output<typeof schema>

const toast = useToast()

async function onSubmit() {
  const payload = {
    name: state.name,
    course_id: state.course_id?.value,  // no .value if reactive
    teacher_id: state.teacher_id?.value, // same here
    schedule: (state.schedule || [])
      .filter(item => item.day && item.time)
      .map(item => `${item.day}-${item.time}`)
      .join(', ')
  }

  console.log(payload);

  groupStore.addGroup(payload)
  toast.add({ title: 'Success', description: `مجموعة جديد ${state.name} تم اضافة بنجاح`, color: 'success' })
  open.value = false
}

const searchTeacherTerm = ref('')
const searchCourseTerm = ref('')

watch(searchCourseTerm, (newVal) => {
  if (newVal.length >= 3 || newVal.length < 1)
    // For example, debounce here if needed to avoid too many requests
    courseStore.loadCoursesForSelect(newVal)
})

watch(searchTeacherTerm, (newVal) => {
  if (newVal.length >= 3 || newVal.length < 1)
    // For example, debounce here if needed to avoid too many requests
    teacherStore.loadTeachersForSelect(newVal)
})

const state = reactive<Partial<Schema>>({
  name: null,
  course_id: null,
  teacher_id: null,
  schedule: [{ day: '', time: '' }]
})

const days = ['السبت', 'الاحد', ' الاثنين', 'الثلاثاء', 'الاربعاء', 'الخميس', 'الجمعة']
// To add a new line
// Add day-time string to the schedule
function addScheduleItem() {
  state.schedule.push({ day: '', time: '' }) // Add a new object with day and time properties
}

// Function to remove a schedule item by its index
function removeScheduleItem(index: number) {
  state.schedule.splice(index, 1)
}
</script>

<template>
  <UModal v-model:open="open" title="اضافة مجموعة" description="إضافة مجموعة جديد" dir="rtl">
    <UButton label="إضافة مجموعة جديد" icon="i-lucide-plus" dir="rtl" />

    <template #body dir="rtl">
      <UForm :schema="schema" :state="state" class="space-y-4" dir="rtl">

        <UFormField label="اسم المجموعة" placeholder="اسم المجموعة" name="name">
          <UInput required v-model="state.name" class="w-full" />
        </UFormField>

        <UFormField label="اسم الكورس" placeholder="اختر الكورس" name="course_id">
          <USelectMenu required dir="rtl" v-model:search-term="searchCourseTerm" v-model="state.course_id"
            :items="courseStore.courseOptions" class="w-full" placeholder="اختر الكورس" :search-input="{
              placeholder: 'بحث...',
              icon: 'i-lucide-search'
            }" />
        </UFormField>

        <UFormField label="اسم المدرس" placeholder="اختر المدرس" name="teacher_id">
          <USelectMenu required v-model:search-term="searchTeacherTerm" v-model="state.teacher_id"
            :items="teacherStore.teacherOptions" class="w-full" placeholder="اختر المدرس" :search-input="{
              placeholder: 'بحث...',
              icon: 'i-lucide-search'
            }" />
        </UFormField>

        <UFormField label="مواعيد المجموعة" placeholder="اسم المجموعة" name="schedule">

          <!-- Loop through each schedule item and display input fields -->
          <div v-for="(item, index) in state.schedule" :key="index" class="flex items-center space-x-4 mb-4 w-full">

            <!-- Select Day -->
            <div class="w-full md:w-4/12">
              <USelect required v-model="state.schedule[index].day" :items="days" size="md" placeholder="اختر اليوم"
                class="w-full" />
            </div>

            <!-- Select Time -->
            <div class="w-full md:w-4/12">
              <UInput required v-model="state.schedule[index].time" type="time" size="md" placeholder="اختر الوقت"
                class="w-full" />
            </div>

            <!-- Remove button -->
            <div class="w-full md:w-4/12 flex justify-start">
              <UButton v-if="index == 0" @click="addScheduleItem" color="primary" icon="i-lucide-plus" label="إضافة وقت"
                size="sm" />
              <UButton v-else color="error" @click="removeScheduleItem(index)" icon="i-lucide-trash" size="sm" />
            </div>
          </div>
        </UFormField>

        <div class="flex justify-end gap-2">
          <UButton label="الغاء" color="neutral" variant="subtle" @click="open = false" />
          <UButton label="حفظ" color="primary" variant="solid" type="submit" @click="onSubmit" />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
