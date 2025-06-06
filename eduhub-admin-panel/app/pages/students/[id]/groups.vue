<script setup lang="ts">
import AddGroup from "~/components/students/AddGroup.vue";

const studentStore = useStudentStore();

const toast = useToast();

const props = defineProps<{
  student: object;
}>();

const groups = ref([]);

onMounted(async () => {
  if (props.student) {
    groups.value = props.student?.groups || [];
  }
});

async function onGroupActiveChange(field, value) {
  await studentStore.enrollmentStatusChange({
    status: value,
    student_id: field.pivot.student_id,
    group_id: field.pivot.group_id,
  });

  // Find the group in the local list and update its pivot.status
  const groupToUpdate = groups.value.find((group) => group.id === field.id);

  if (groupToUpdate && groupToUpdate.pivot) {
    groupToUpdate.pivot.status = value;
  }

  toast.add({
    title: "Success",
    description: `تم تغير حالة المجموعة بنجاح`,
    color: "success",
  });
}

async function onGroupEnrollmentDelete(field) {
  // Do something with data
  await studentStore.deleteEnrollment({
    student_id: field.pivot.student_id,
    group_id: field.pivot.group_id,
  });

  const index = groups.value.findIndex((group) => group.id === field.id);
  if (index !== -1) {
    groups.value.splice(index, 1);

    toast.add({
      title: "Success",
      description: `تم جذف المجموعة بنجاح`,
      color: "success",
    });
  }
}

async function handleUpdateStudent(updatedStudent: object) {
  await studentStore.loadOneStudent(props.student.id);
  groups.value = studentStore.item?.groups || [];
}
</script>

<template>
  <UPageCard
    title="المجموعات الخاصة بالطالب"
    :description="student?.name"
    variant="naked"
    orientation="horizontal"
    class="m-2"
  >
    <AddGroup :student="student" @updateStudent="handleUpdateStudent" />
  </UPageCard>

  <UPageCard
    v-for="field in groups"
    :key="field.id"
    variant="soft"
    style="font-size: 18px; width: 100%"
    class="p-2 shadow-sm border border-gray-200 rounded-2xl hover:shadow-md transition-all"
  >
    <div
      class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2"
    >
      <!-- Left: Group Info -->
      <div class="flex-1 space-y-2 text-lg leading-relaxed">
        <h3 class="text-2xl font-bold text-primary">{{ field.name }}</h3>

        <p>
          <span class="font-bold">الكورس: </span>
          {{ field.course?.name || "غير متوفر" }}
        </p>

        <p>
          <span class="font-bold">المدرس: </span>
          {{ field.teacher?.name || "غير متوفر" }}
        </p>

        <p class="flex items-center text-lg">
          <span style="margin-left: 20px">
            <span class="font-bold">تاريخ البدء: </span>
            {{ field.pivot.start_date || "غير متوفر" }}
          </span>
          <span>
            <span class="font-bold">تاريخ الانتهاء: </span>
            {{ field.pivot.end_date || "غير متوفر" }}
          </span>
        </p>

        <p>
          <span class="font-bold">أيام الجدول: </span>
          <span v-if="field.schedule" class="text-primary">
            {{
              field.schedule
                .split(",")
                .map((day) => day.trim())
                .join(" , ")
            }}
          </span>
          <span v-else>غير متوفر</span>
        </p>
      </div>

      <div class="pt-2 sm:pt-0 flex items-center gap-4">
        <USwitch
          :model-value="field.pivot.status"
          @update:model-value="(value) => onGroupActiveChange(field, value)"
        />
        <UButton
          icon="i-lucide-trash"
          color="red"
          variant="soft"
          size="lg"
          @click="onGroupEnrollmentDelete(field)"
          class="hover:bg-red-100 hover:text-red-700 transition-colors duration-200"
        />
      </div>
    </div>
  </UPageCard>
</template>
