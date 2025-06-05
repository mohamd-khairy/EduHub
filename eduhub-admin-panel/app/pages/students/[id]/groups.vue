<script setup lang="ts">
const props = defineProps<{
  student: object;
}>();

const groups = ref([]);

onMounted(async () => {
  if (props.student) {
    groups.value = props.student?.groups || [];

    console.log("Groups:", groups.value);
  }
});

const state = reactive<{ [key: string]: boolean }>({
  name: true,
  schedule: false,
  start_date: true,
  end_date: false,
  teacher: true,
});

async function onChange() {
  // Do something with data
  console.log(state);
}

async function onDelete() {
  // Do something with data
  console.log(state);
}
</script>

<template>
  <UPageCard
    title="المجموعات"
    description="المجموعات الخاصة بالطالب"
    variant="naked"
    orientation="horizontal"
    class="m-2"
  >
    <UButton
      form="settings"
      label="اضافة مجموعة"
      color="neutral"
      type="submit"
      class="w-fit lg:ms-auto"
      style="font-size: 18px"
    />
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
          :model-value="field.active"
          @update:model-value="(value) => onChange(field, value)"
        />
        <UButton
          icon="i-lucide-trash"
          color="red"
          variant="soft"
          size="lg"
          @click="onDelete(field)"
          class="hover:bg-red-100 hover:text-red-700 transition-colors duration-200"
        />
      </div>
    </div>
  </UPageCard>
</template>
