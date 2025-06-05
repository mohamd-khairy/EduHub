// File: components/students/StudentDetails.vue
<script setup lang="ts">
import { computed, watch } from "vue";
import { useRoute } from "vue-router";
import AddModal from '~/components/students/AddModal.vue'

const emit = defineEmits(["updateStudent"]);

const props = defineProps<{
  student: object;
}>();

const route = useRoute();
const studentId = computed(() => route.params.id as string);

const links = computed(() => [
  [
    {
      label: "الملف الشخصي",
      icon: "i-lucide-user",
      to: `/students/${studentId.value}`,
      exact: true,
    },
    {
      label: "المجموعات",
      icon: "i-lucide-users",
      to: `/students/${studentId.value}/groups`,
    },
    {
      label: "الامتحانات",
      icon: "i-lucide-bell",
      to: `/students/${studentId.value}/members`,
    },
    {
      label: "الحضور والانصراف",
      icon: "i-lucide-shield",
      to: `/students/${studentId.value}/security`,
    },
  ],
]);

function handleUpdateStudent(updatedStudent: object) {
  emit("updateStudent", updatedStudent);
}

function onAddStudent() {
  // Logic to add a new student
  console.log("Add Student button clicked");
}
</script>

<template>
  <UDashboardPanel id="inbox-2">
    <template #header>
      <UDashboardNavbar title="تفاصيل الطالب" :toggle="false">
        <template #right>
            <AddModal />
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <UNavigationMenu :items="links" highlight class="-mx-1 flex-1" />
      </UDashboardToolbar>
    </template>
    <template #body>
      <div
        class="flex flex-col gap-4 sm:gap-6 lg:gap-12 w-full lg:max-w-2xl mx-auto"
      >
        <NuxtPage :student="student" @updateStudent="handleUpdateStudent" />
      </div>
    </template>
  </UDashboardPanel>
</template>
