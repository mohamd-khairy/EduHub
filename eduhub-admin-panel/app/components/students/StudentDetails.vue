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
      label: "QR Code",
      icon: "i-lucide-qr-code",
      to: `/students/${studentId.value}/qr`,
      permission: "read-student-payment",
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
  const filteredLinks = allLinks.filter((link) => {
    if(link.permission == 'read-student-parent' && props.student?.parent == null)
      return false
    if(link.permission == 'read-student-payment' && props.student?.payments?.length == 0)
      return false
    if(link.permission == 'read-student-exam' && props.student?.groups?.length == 0)
      return false
    if(link.permission == 'read-student-attendance' && props.student?.groups?.length == 0)
      return false
    
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
      <UDashboardNavbar
        :title="`تفاصيل الطالب :  ${student?.name || ''}`"
        :toggle="true"
      >
        <template #right>
          <UButton
            label="تصدير الطلاب"
            loading-auto
            color="success"
            variant="outline"
            trailing-icon="i-lucide-file-spreadsheet"
            v-if="authStore.hasPermission('read-student')"
            @click="exportToExcel('student')"
          />
          <AddModal @addStudent="handleAddStudent" />
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <UNavigationMenu :items="links" highlight class="-mx-1 flex-1" />
      </UDashboardToolbar>
    </template>
    <template #body>
      <div
        class="flex flex-col gap-4 sm:gap-6 lg:gap-12 w-full lg:max-w-4xl mx-auto"
      >
        <NuxtPage :student="student" @updateStudent="handleUpdateStudent" />
      </div>
    </template>
  </UDashboardPanel>
</template>
