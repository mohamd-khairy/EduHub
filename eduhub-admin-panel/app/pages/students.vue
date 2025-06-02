// File: pages/students.vue
<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core'
import type { Mail } from '~/types'
import StudentList from '~/components/students/StudentList.vue'
import StudentDetails from '~/components/students/StudentDetails.vue'

const allStudents = ref<Mail[]>([])
const filteredStudents = ref<Mail[]>([])
const search = ref('')
const pagination = ref({
  page: 1,
  pageCount: 1,
  pageSize: 10,
  total: 0
})

const selectedStudent = ref<Mail | null>(null)
const isMailPanelOpen = computed({
  get: () => !!selectedStudent.value,
  set: (val: boolean) => {
    if (!val) selectedStudent.value = null
  }
})

async function loadData(page = 1) {
  const { data } = await useFetch(`http://localhost/EduHub/eduhub-backend/public/api/student?page=${page}`, {
    transform: (res) => res.data
  })
  if (data.value) {
    allStudents.value = data.value.data
    filteredStudents.value = [...allStudents.value]
    pagination.value = {
      page: data.value.current_page,
      pageCount: data.value.last_page,
      pageSize: data.value.per_page,
      total: data.value.total
    }
  }
}

await loadData()

function setFilterValue(value: string) {
  if (value?.length >= 1) {
    search.value = value
    filteredStudents.value = allStudents.value.filter(student =>
      student.name.toLowerCase().includes(value.toLowerCase())
    )
  } else {
    filteredStudents.value = allStudents.value
  }
}

const breakpoints = useBreakpoints(breakpointsTailwind)
const isMobile = breakpoints.smaller('lg')
</script>

<template>
  <UDashboardPanel id="inbox-1" :default-size="25" :min-size="20" :max-size="30" resizable>
    <UDashboardNavbar title="الطلاب">
      <template #leading>
        <UDashboardSidebarCollapse />
      </template>
      <template #right>
        <UInput class="max-w-sm" icon="i-lucide-search" placeholder="ابحث ..." @update:model-value="setFilterValue($event)" />
      </template>
    </UDashboardNavbar>
    <StudentList v-model="selectedStudent" :students="filteredStudents" />
  </UDashboardPanel>

  <StudentDetails v-if="selectedStudent" :student="selectedStudent" />
  <div v-else class="hidden lg:flex flex-1 items-center justify-center">
    <UIcon name="i-lucide-inbox" class="size-32 text-dimmed" />
  </div>

  <ClientOnly>
    <USlideover v-if="isMobile" v-model:open="isMailPanelOpen">
      <template #content>
        <StudentDetails v-if="selectedStudent" :student="selectedStudent" />
      </template>
    </USlideover>
  </ClientOnly>
</template>
