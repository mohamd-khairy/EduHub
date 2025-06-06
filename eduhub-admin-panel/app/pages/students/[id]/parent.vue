<script setup lang="ts">
const studentStore = useStudentStore();

const props = defineProps<{
  student: object;
}>();
</script>

<template>
  <UPageCard
    title="تفاصيل ولي امر الطالب"
    :description="student.name"
    variant="naked"
    orientation="horizontal"
    class="mt-2 "
  >
  </UPageCard>

  <UDashboardNavbar title="اسم ولي الامر" :toggle="false">
    <template #right>
      {{ student.parent.name }}
    </template>
  </UDashboardNavbar>
  <UDashboardNavbar title="البريد الالكتروني" :toggle="false">
    <template #right>
      {{ student.parent.email }}
    </template>
  </UDashboardNavbar>
  <UDashboardNavbar title="رقم التليفون" :toggle="false">
    <template #right>
      {{ student.parent.phone }}
    </template>
  </UDashboardNavbar>
  <UDashboardNavbar title="عنوان ولي الامر" :toggle="false">
    <template #right>
      {{ student.parent.address }}
    </template>
  </UDashboardNavbar>
</template>
