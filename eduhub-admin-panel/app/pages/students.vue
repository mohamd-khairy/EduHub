<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { breakpointsTailwind } from '@vueuse/core'
import type { Mail } from '~/types'
import StudentList from '~/components/students/StudentList.vue'
import StudentDetails from '~/components/students/StudentDetails.vue'

const allStudents = ref<object[]>([]) // ← البيانات الأصلية الكاملة
const filteredStudents = ref<object[]>([]) // ← البيانات المعروضة بعد الفلترة
const search = ref('')
const pagination = ref({
  page: 1,
  pageCount: 1,
  pageSize: 10,
  total: 0
})

// Initial load
await loadData()

async function loadData(page = 1) {
  const { data } = await useFetch(`http://localhost/EduHub/eduhub-backend/public/api/student?page=${page}`, {
    transform: (res) => res.data
  })

  if (data.value) {
    allStudents.value = data.value?.data
    filteredStudents.value = [...allStudents.value]


    pagination.value = {
      page: data.value.current_page,
      pageCount: data.value.last_page,
      pageSize: data.value.per_page,
      total: data.value.total
    }
  }
}

const selectedMail = ref<Mail | null>()

const isMailPanelOpen = computed({
  get() {
    return !!selectedMail.value
  },
  set(value: boolean) {
    if (!value) {
      selectedMail.value = null
    }
  }
})

function setFilterValue(value: string) {
  if (value?.length >= 1) {
    search.value = value
    filteredStudents.value = allStudents.value.filter(student =>
      student.name.toLowerCase().includes(value.toLowerCase())
    )
  } else {
    filteredStudents.value = allStudents
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

        <UInput class="max-w-sm" icon="i-lucide-search" placeholder="ابحث ..."
          @update:model-value="setFilterValue($event)" />

      </template>
    </UDashboardNavbar>
    <StudentList v-model="selectedMail" :mails="filteredStudents" />
  </UDashboardPanel>

  <StudentDetails v-if="selectedMail" :mail="selectedMail" @close="selectedMail = null" />
  <div v-else class="hidden lg:flex flex-1 items-center justify-center">
    <UIcon name="i-lucide-inbox" class="size-32 text-dimmed" />
  </div>

  <ClientOnly>
    <USlideover v-if="isMobile" v-model:open="isMailPanelOpen">
      <template #content>
        <StudentDetails v-if="selectedMail" :mail="selectedMail" @close="selectedMail = null" />
      </template>
    </USlideover>
  </ClientOnly>
</template>
