// File: components/students/StudentDetails.vue
<script setup lang="ts">
import { computed, watch } from "vue";
import { useRoute } from "vue-router";
import AddModal from "~/components/students/AddModal.vue";

const emit = defineEmits(["updateStudent", "addStudent"]);
const authStore = useAuthStore();

const props = defineProps<{
  student: object;
}>();

const route = useRoute();
const studentId = computed(() => route.params.id as string);

const links = computed(() => {
  // Define the links that need permission check
  const allLinks = [
    {
      label: "بيانات الطالب",
      icon: "i-lucide-user",
      to: `/students/${studentId.value}`,
      exact: true,
      permission: "read-student",
    },
    {
      label: "ولي الامر",
      icon: "i-lucide-user",
      to: `/students/${studentId.value}/parent`,
      permission: "read-student-parent",
    },
    {
      label: "المجموعات",
      icon: "i-lucide-folders",
      to: `/students/${studentId.value}/groups`,
      permission: "read-student-group",
    },
    {
      label: "الاختبارات",
      icon: "i-lucide-bell",
      to: `/students/${studentId.value}/exams`,
      permission: "read-student-exam",
    },
    {
      label: "الحضور والغياب",
      icon: "i-lucide-shield",
      to: `/students/${studentId.value}/attendance`,
      permission: "read-student-attendance",
    },
    {
      label: "المدفوعات",
      icon: "i-lucide-dollar-sign",
      to: `/students/${studentId.value}/payments`,
      permission: "read-student-payment",
    },
  ];

  // Filter the links based on user's permissions
  const filteredLinks = allLinks.filter(link => {
    // Check if user has the permission for the link
    return authStore.permissions.includes(link.permission);
  });

  // Return the filtered links
  return [filteredLinks]; // Returning the links wrapped in an array, similar to your original structure
});


function handleUpdateStudent(updatedStudent: object) {
  emit("updateStudent", updatedStudent);
}

function handleAddStudent(addStudent: object) {
  // Logic to add a new student
  emit("addStudent", addStudent);
}
</script>

<template>
  <UDashboardPanel id="inbox-2">
    <template #header>
      <UDashboardNavbar :title="`تفاصيل الطالب :  ${student?.name || ''}`" :toggle="true">
        <template #right>
          <AddModal @addStudent="handleAddStudent" />
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <UNavigationMenu :items="links" highlight class="-mx-1 flex-1" />
      </UDashboardToolbar>
    </template>
    <template #body>
      <div class="flex flex-col gap-4 sm:gap-6 lg:gap-12 w-full lg:max-w-4xl mx-auto">
        <NuxtPage :student="student" @updateStudent="handleUpdateStudent" />
      </div>
    </template>
  </UDashboardPanel>
</template>
