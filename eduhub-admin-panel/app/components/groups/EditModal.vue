<script setup lang="ts">
import * as z from "zod";

const props = defineProps({
  open: Boolean,
  item: Object,
});

const emit = defineEmits(["update:open"]);

watch(
  () => props.open,
  (val) => {
    open.value = val;
  }
);

const open = ref(false);

watch(open, (val) => {
  emit("update:open", val);
});

const groupStore = useGroupStore();
const courseStore = useCourseStore();
const teacherStore = useTeacherStore();

const toast = useToast();

async function onSubmit() {
  const payload = {
    name: state.name,
    course_id: state.course_id?.value || props.item?.course_id, // no .value if reactive
    teacher_id: state.teacher_id?.value || props.item?.teacher_id, // same here
    schedules: state.schedules,
  };

  groupStore.editGroup(payload, props.item?.id);

  toast.add({
    title: "Success",
    description: `مجموعة  ${state.name} تم تعديل بنجاح`,
    color: "success",
  });
  open.value = false;
}

const searchTeacherTerm = ref("");
const searchCourseTerm = ref("");

watch(searchCourseTerm, (newVal) => {
  if (newVal.length >= 3 || newVal.length < 1)
    // For example, debounce here if needed to avoid too many requests
    courseStore.loadCoursesForSelect(newVal);
});

watch(searchTeacherTerm, (newVal) => {
  if (newVal.length >= 3 || newVal.length < 1)
    // For example, debounce here if needed to avoid too many requests
    teacherStore.loadTeachersForSelect(newVal);
});

const schema = z.object({
  name: z.string().min(2, "Too short"),
});

type Schema = z.output<typeof schema>;

const state = reactive<Partial<Schema>>({
  name: null,
  course_id: null,
  teacher_id: null,
  schedules: [],
});

const days = [
  "السبت",
  "الاحد",
  "الاثنين",
  "الثلاثاء",
  "الاربعاء",
  "الخميس",
  "الجمعة",
];
// To add a new line
function addScheduleItem() {
  state.schedules.push({}); // Add a new object with day and time properties
}

// Function to remove a schedules item by its index
function removeScheduleItem(index: number) {
  state.schedules.splice(index, 1);
}

watch(
  () => props.item,
  (val) => {
    if (!val) return;
    state.name = val.name || "";
    state.course_id = val.course?.name || null;
    state.teacher_id = val.teacher?.name || null;
    state.schedules = val.schedules;    
  },
  { immediate: true, deep: true }
);
</script>

<template>
  <UModal
    v-model:open="open"
    title="تعديل مجموعة"
    description="تعديل مجموعة "
    dir="rtl"
  >
    <template #body dir="rtl">
      <UForm :schema="schema" :state="state" class="space-y-4" dir="rtl">
        <UFormField label="اسم المجموعة" placeholder="اسم المجموعة" name="name">
          <UInput required v-model="state.name" class="w-full" />
        </UFormField>

        <UFormField
          label="اسم الكورس"
          placeholder="اختر الكورس"
          name="course_id"
        >
          <USelectMenu
            required
            dir="rtl"
            v-model:search-term="searchCourseTerm"
            v-model="state.course_id"
            :items="courseStore.courseOptions"
            class="w-full"
            placeholder="اختر الكورس"
            :search-input="{
              placeholder: 'بحث...',
              icon: 'i-lucide-search',
            }"
          />
        </UFormField>

        <UFormField
          label="اسم المدرس"
          placeholder="اختر المدرس"
          name="teacher_id"
        >
          <USelectMenu
            required
            v-model:search-term="searchTeacherTerm"
            v-model="state.teacher_id"
            :items="teacherStore.teacherOptions"
            class="w-full"
            placeholder="اختر المدرس"
            :search-input="{
              placeholder: 'بحث...',
              icon: 'i-lucide-search',
            }"
          />
        </UFormField>

        <UFormField name="schedules">
          <div class="flex justify-between items-center mb-4">
            <span class="text-lg font-semibold">مواعيد المجموعة</span>
            <UButton
              @click="addScheduleItem"
              color="primary"
              icon="i-lucide-plus"
              label="إضافة وقت"
              size="sm"
            />
          </div>

          <div
            v-for="(item, index) in state.schedules"
            :key="index"
            class="flex items-center space-x-4 mb-4 w-full"
          >
            <!-- Select Day -->
            <div class="w-full md:w-4/12">
              <USelect
                required
                v-model="item.day"
                :items="days"
                size="md"
                placeholder=" اليوم"
                class="w-full"
              />
            </div>

            <!-- Select Time -->
            <div class="w-full md:w-4/12">
              <UInput
                required
                v-model="item.start_time"
                title="وقت البدء"
                type="time"
                size="md"
                placeholder=" الوقت"
                class="w-full"
              />
            </div>
            <div class="w-full md:w-4/12">
              <UInput
                required
                v-model="item.end_time"
                title="وقت الانتهاء"
                type="time"
                size="md"
                placeholder=" الوقت"
                class="w-full"
              />
            </div>

            <div class="w-full md:w-4/12">
              <USelect
                required
                v-model="item.room_id"
                :items="[1, 2, 3, 4, 5, 6, 7]"
                size="md"
                placeholder=" الغرفة"
                class="w-full"
              />
            </div>

            <!-- Remove button -->
            <div class="w-full md:w-4/12 flex justify-start">
              <UButton
                v-if="index > 0"
                color="error"
                @click="removeScheduleItem(index)"
                icon="i-lucide-trash"
                size="sm"
              />
            </div>
          </div>
        </UFormField>

        <div class="flex justify-end gap-2">
          <UButton
            label="الغاء"
            color="neutral"
            variant="subtle"
            @click="open = false"
          />
          <UButton
            label="حفظ"
            color="primary"
            variant="solid"
            type="submit"
            loading-auto
            @click="onSubmit"
          />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
