// File: pages/students.vue
<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core'
import StudentList from '~/components/students/StudentList.vue'
import StudentDetails from '~/components/students/StudentDetails.vue'

const studentStore = useStudentStore()
const currentPage = ref(1)
const pageSize = 20 // or whatever you want
const isLoading = ref(false)
const hasMore = ref(true)

const allStudents = ref<object[]>([])
const filteredStudents = ref<object[]>([])
const search = ref('')
const selectedStudent = ref<object | null>(null)
const isMailPanelOpen = computed({
  get: () => !!selectedStudent.value,
  set: (val: boolean) => {
    if (!val) selectedStudent.value = null
  }
})

onMounted(async () => {
  isLoading.value = true
  await studentStore.loadAllStudents(currentPage.value)
  allStudents.value = studentStore.items
  filteredStudents.value = allStudents.value
  currentPage.value++
  isLoading.value = false
})

async function setFilterValue(value: string) {
  if (value?.length >= 1) {
    search.value = value
    await studentStore.loadAllStudents(currentPage.value, null, { name: value.toLowerCase()})
    filteredStudents.value = studentStore.items

    // filteredStudents.value = allStudents.value.filter(student =>
    //   student.name.toLowerCase().includes(value.toLowerCase())
    // )
  } else {
    filteredStudents.value = allStudents.value
  }
}

const breakpoints = useBreakpoints(breakpointsTailwind)
const isMobile = breakpoints.smaller('lg')

const scrollContainer = ref<HTMLElement | null>(null)

function onScroll() {
  if (!scrollContainer.value) return

  const { scrollTop, scrollHeight, clientHeight } = scrollContainer.value

  // Trigger loading when scrolled within 150px of bottom
  if (scrollTop + clientHeight >= scrollHeight - 150) {
    loadNextPage()
  }
}

async function loadNextPage() {
  if (isLoading.value || !hasMore.value) return
  isLoading.value = true

  await studentStore.loadAllStudents(currentPage.value)
  const newStudents = studentStore.items

  if (newStudents?.length < pageSize) {
    hasMore.value = false // no more data
  }

  allStudents.value.push(...newStudents)
  filteredStudents.value = allStudents.value // or reapply filter if needed
  currentPage.value++
  isLoading.value = false
}

</script>

<template>
  <UDashboardPanel id="inbox-1" :default-size="25" :min-size="20" :max-size="30" resizable>
    <UDashboardNavbar title="الطلاب">
      <template #leading>
        <UDashboardSidebarCollapse />
      </template>
      <template #right>
        <UInput class="max-w-sm" icon="i-lucide-search" placeholder="ابحث ..."
          @update:model-value="setFilterValue($event)" />
      </template>
    </UDashboardNavbar>

    <div ref="scrollContainer" @scroll="onScroll" style="overflow-y: auto;">
      <StudentList v-model="selectedStudent" :students="filteredStudents" />
      <div v-if="isLoading" class="text-center p-2">Loading...</div>
    </div>

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
