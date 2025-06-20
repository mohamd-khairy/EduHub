<script setup lang="ts">
definePageMeta({
  permission: "read-student",
});
import { ref, computed, watch, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { breakpointsTailwind, useBreakpoints } from "@vueuse/core";
import StudentList from "~/components/students/StudentList.vue";
import StudentDetails from "~/components/students/StudentDetails.vue";

const studentStore = useStudentStore();
const authStore = useAuthStore();

const currentPage = ref(1);
const pageSize = 10;
const isLoading = ref(false);
const isEmpty = ref(false);
const hasMore = ref(true);
const router = useRouter();
const allStudents = ref<object[]>([]);
const filteredStudents = ref<object[]>([]);
const search = ref("");
const selectedStudent = ref<object | null>(null);
const route = useRoute();
const studentIdFromRoute = computed(() => route.params.id);
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
  if (allStudents.value.length === 0) {
    isEmpty.value = true;
  }

  const idInRoute = studentIdFromRoute.value;
  // 🧠 Try to match route param
  if (idInRoute) {
    await studentStore.loadInformation(idInRoute);
    selectedStudent.value = studentStore.information;
  } else if (allStudents.value.length > 0) {
    await studentStore.loadInformation(allStudents.value[0].id);
    selectedStudent.value = studentStore.information;
    router.push(`/students/${selectedStudent.value.id}`);
  }
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

async function handleAddStudent(addStudent: object) {
  // Logic to add a new student
  console.log("addStudent", addStudent);
  isLoading.value = true;
  currentPage.value = 1;
  await studentStore.loadAllStudents(currentPage.value);
  allStudents.value = studentStore.items;
  filteredStudents.value = allStudents.value;
  currentPage.value++;
  isLoading.value = false;

  // 👇 Select the first student automatically
  if (allStudents.value.length > 0) {
    selectedStudent.value = allStudents.value[0];
    router.push(`/students/${selectedStudent.value.id}`);
  }
}
</script>

<template>
  <UDashboardPanel
    v-if="selectedStudent"
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
    @addStudent="handleAddStudent"
  />


  <div v-else-if="!isEmpty && !selectedStudent" class="hidden lg:flex flex-col items-center justify-center flex-1 gap-4 text-center p-8">
    <span
      class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-gray-900"
    ></span>
  </div>

  <div
    v-else-if="isEmpty"
    class="hidden lg:flex flex-col items-center justify-center flex-1 gap-4 text-center p-8"
  >
    <!-- Icon -->
    <UIcon name="i-lucide-inbox" class="size-32 text-gray-400" />

    <!-- Description -->
    <div>
      <h2 class="text-xl font-semibold text-gray-600 mb-1">لا يوجد طلاب</h2>
      <p class="text-sm text-gray-500">
        لم يتم إضافة أي طالب بعد. يمكنك البدء الآن.
      </p>
    </div>

    <!-- Action Button -->
    <UButton
      label="إضافة طالب"
      color="primary"
      icon="i-lucide-plus"
      @click="onAddStudent"
      size="lg"
      class="mt-2"
      v-if="authStore.permissions.includes('create-student')"
    />
  </div>

  <ClientOnly>
    <USlideover v-if="isMobile" v-model:open="isMailPanelOpen">
      <template #content>
        <StudentDetails
          v-if="selectedStudent"
          :student="selectedStudent"
          @updateStudent="handleUpdateStudent"
          @addStudent="handleAddStudent"
        />
      </template>
    </USlideover>
  </ClientOnly>
</template>
