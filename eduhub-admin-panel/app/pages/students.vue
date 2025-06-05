<script setup lang="ts">
import { ref, computed, watch, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { breakpointsTailwind, useBreakpoints } from "@vueuse/core";
import StudentList from "~/components/students/StudentList.vue";
import StudentDetails from "~/components/students/StudentDetails.vue";

const studentStore = useStudentStore();
const currentPage = ref(1);
const pageSize = 10;
const isLoading = ref(false);
const hasMore = ref(true);

const allStudents = ref<object[]>([]);
const filteredStudents = ref<object[]>([]);
const search = ref("");
const selectedStudent = ref<object | null>(null);
const isMailPanelOpen = computed({
  get: () => !!selectedStudent.value,
  set: (val: boolean) => {
    if (!val) selectedStudent.value = null;
  },
});

let debounceTimer: ReturnType<typeof setTimeout>;

async function setFilterValue(value: string) {
  clearTimeout(debounceTimer); // Clear previous timer

  debounceTimer = setTimeout(async () => {
    if (value?.length >= 3) {
      search.value = value;
      await studentStore.loadAllStudents(1, null, {
        name: value.toLowerCase(),
      });
      filteredStudents.value = studentStore.items;
    } else {
      filteredStudents.value = allStudents.value;
    }
  }, 1000); // 1000 ms = 1 second
}

// Handle the update event from StudentDetails component
function handleUpdateStudent(updatedStudent: object) {
  // Find the index of the student that was updated
  const index = allStudents.value.findIndex(
    (student: any) => student.id === updatedStudent.id
  );

  // If the student is found, update the student in the allStudents array
  if (index !== -1) {
    allStudents.value[index] = updatedStudent;
    allStudents.value[index]["parent"]["name"] = updatedStudent.parent_id.label; // Ensure parent data is also updated
    allStudents.value[index]["parent"]["id"] = updatedStudent.parent_id.value; // Ensure parent data is also updated
    filteredStudents.value = allStudents.value; // Reapply the filter if necessary
  }
}

// Load initial data
onMounted(async () => {
  isLoading.value = true;
  await studentStore.loadAllStudents(currentPage.value);
  allStudents.value = studentStore.items;
  filteredStudents.value = allStudents.value;
  currentPage.value++;
  isLoading.value = false;
});

const scrollContainer = ref<HTMLElement | null>(null);

function onScroll() {
  if (!scrollContainer.value) return;

  const { scrollTop, scrollHeight, clientHeight } = scrollContainer.value;

  // Trigger loading when scrolled within 150px of bottom
  if (scrollTop + clientHeight >= scrollHeight - 150 && hasMore.value) {
    loadNextPage();
  }
}

async function loadNextPage() {
  if (isLoading.value || !hasMore.value) return;
  isLoading.value = true;

  await studentStore.loadAllStudents(currentPage.value);
  const newStudents = studentStore.items;

  if (allStudents.value?.length >= studentStore.pagination?.total) {
    hasMore.value = false; // no more data
  }

  allStudents.value.push(...newStudents);
  filteredStudents.value = allStudents.value; // or reapply filter if needed
  currentPage.value++;
  isLoading.value = false;
}
</script>

<template>
  <UDashboardPanel
    id="inbox-1"
    :default-size="25"
    :min-size="20"
    :max-size="30"
    resizable
  >
    <UDashboardNavbar title="الطلاب">
      <template #leading>
        <UDashboardSidebarCollapse />
      </template>
      <template #right>
        <UInput
          class="max-w-sm"
          icon="i-lucide-search"
          placeholder="ابحث ..."
          @update:model-value="setFilterValue($event)"
        />
      </template>
    </UDashboardNavbar>

    <div ref="scrollContainer" @scroll="onScroll" style="overflow-y: auto">
      <StudentList v-model="selectedStudent" :students="filteredStudents" />
    </div>
  </UDashboardPanel>

  <!-- StudentDetails Component with Event Handling -->
  <StudentDetails
    v-if="selectedStudent"
    :student="selectedStudent"
    @updateStudent="handleUpdateStudent"
  />

  <div v-else class="hidden lg:flex flex-1 items-center justify-center">
    <UIcon name="i-lucide-inbox" class="size-32 text-dimmed" />
  </div>

  <ClientOnly>
    <USlideover v-if="isMobile" v-model:open="isMailPanelOpen">
      <template #content>
        <StudentDetails
          v-if="selectedStudent"
          :student="selectedStudent"
          @updateStudent="handleUpdateStudent"
        />
      </template>
    </USlideover>
  </ClientOnly>
</template>
