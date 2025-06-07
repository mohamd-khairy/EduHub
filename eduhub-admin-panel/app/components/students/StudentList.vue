// File: components/students/StudentList.vue
<script setup lang="ts">
import { format, isToday } from "date-fns";
import { useRouter } from "vue-router";

const studentStore = useStudentStore();

const props = defineProps<{
  students: [];
}>();

const studentsRefs = ref<Element[]>([]);
const selectedStudent = defineModel<null>();
const router = useRouter();

async function handleClick(student) {
  await studentStore.loadInformation(student.id);
  selectedStudent.value = studentStore.information;
  router.push(`/students/${student.id}`);
}

watch(selectedStudent, () => {
  if (!selectedStudent.value) return;
  const ref = studentsRefs.value[selectedStudent.value.id];
  if (ref) ref.scrollIntoView({ block: "nearest" });
});
</script>

<template>
  <div class="overflow-y-auto divide-y divide-default">
    <div
      v-for="(student, index) in students"
      :key="index"
      :ref="el => { studentsRefs[student.id] = el as Element }"
      class="p-4 sm:px-6 text-sm cursor-pointer border-l-2 transition-colors"
      :class="[
        student?.unread ? 'text-highlighted' : 'text-toned)',
        selectedStudent && selectedStudent.id === student.id
          ? 'border-primary bg-primary/10'
          : 'border-(--ui-bg) hover:border-primary hover:bg-primary/5',
      ]"
      @click="handleClick(student)"
    >
      <div
        class="flex text-start justify-between font-semibold text-lg"
        style="text-align: right"
      >
        <div class="flex gap-4">
          <span>{{ student.id }}#</span>

          <UAvatar :src="student.image" :alt="student.name" size="lg" />
          {{ student.name }}
        </div>
        <!-- <span>{{
          format(new Date(student.created_at), "MMM-yyyy", { locale: arSA })
        }}</span> -->
      </div>
      <!-- <p class="truncate font-semibold">
        ({{ student.phone }}) - ({{ student.email }})
      </p>
      <p class="text-dimmed line-clamp-1">
        ({{ student.grade_level }}) - ({{ student.school_name }})
      </p> -->
    </div>
  </div>
</template>
