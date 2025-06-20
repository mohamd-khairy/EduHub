<script setup lang="ts">
const studentStore = useStudentStore();
const student_id = ref(null);
const students = ref([]);
onMounted(async () => {
  await studentStore.loadStudentsForSelect();
  students.value = studentStore.studentOptions;
});
</script>

<template>
  <USelect
    placeholder="اختر الطالب"
    v-model="student_id"
    :items="students"
    variant="ghost"
    class="w-34"
  />
</template>
