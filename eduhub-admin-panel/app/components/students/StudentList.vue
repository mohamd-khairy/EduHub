
// File: components/students/StudentList.vue
<script setup lang="ts">
import { format, isToday } from 'date-fns'
import { useRouter } from 'vue-router'
import type { Mail } from '~/types'

const props = defineProps<{
  students: Mail[]
}>()

const mailsRefs = ref<Element[]>([])
const selectedStudent = defineModel<Mail | null>()
const router = useRouter()

function handleClick(mail: Mail) {
  selectedStudent.value = mail
  router.push(`/students/${mail.id}`)
}

watch(selectedStudent, () => {
  if (!selectedStudent.value) return
  const ref = mailsRefs.value[selectedStudent.value.id]
  if (ref) ref.scrollIntoView({ block: 'nearest' })
})
</script>

<template>
  <div class="overflow-y-auto divide-y divide-default">
    <div
      v-for="(mail, index) in students"
      :key="index"
      :ref="el => { mailsRefs[mail.id] = el as Element }"
      class="p-4 sm:px-6 text-sm cursor-pointer border-l-2 transition-colors"
      :class="[
        mail?.unread ? 'text-highlighted' : 'text-toned)',
        selectedStudent && selectedStudent.id === mail.id ? 'border-primary bg-primary/10' : 'border-(--ui-bg) hover:border-primary hover:bg-primary/5'
      ]"
      @click="handleClick(mail)"
    >
      <div class="flex items-center justify-between font-semibold text-lg">
        {{ mail.name }}
        <span>{{ format(new Date(mail.created_at), 'MMM-yyyy', { locale: arSA }) }}</span>
      </div>
      <p class="truncate font-semibold">({{ mail.phone }}) - ({{ mail.email }})</p>
      <p class="text-dimmed line-clamp-1">({{ mail.grade_level }}) - ({{ mail.school_name }})</p>
    </div>
  </div>
</template>
