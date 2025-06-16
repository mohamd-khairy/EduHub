<script setup lang="ts">
const route = useRoute();
const open = ref(false);

const links = [
  [
    {
      class: "text-lg",
      label: "الصفحة الرئيسية",
      icon: "i-lucide-house",
      to: "/",
      permission: "read-dashboard",
      onSelect: () => {
        open.value = false;
      },
    },
    {
      class: "text-lg",
      label: "المواد الدراسية",
      icon: "i-lucide-book-open",
      to: "/courses",
      permission: "read-course",
      onSelect: () => {
        open.value = false;
      },
    },
    {
      class: "text-lg",
      label: "المدرسيين",
      icon: "i-lucide-users",
      to: "/teachers",
      permission: "read-teacher",
      onSelect: () => {
        open.value = false;
      },
    },
    {
      class: "text-lg",
      label: "أولياء الامور",
      icon: "i-lucide-users",
      to: "/parents",
      permission: "read-parent",
      onSelect: () => {
        open.value = false;
      },
    },
    {
      class: "text-lg",
      label: "الطلاب",
      icon: "i-heroicons-user-group",
      to: "/students",
      permission: "read-student",
      // badge: '4',
      onSelect: () => {
        open.value = false;
      },
    },
    {
      class: "text-lg",
      label: "المجموعات",
      icon: "i-lucide-group",
      to: "/groups",
      permission: "read-group",
      // badge: '4',
      onSelect: () => {
        open.value = false;
      },
    },
    {
      class: "text-lg",
      label: "الاختبارات",
      icon: "i-lucide-book-open",
      to: "/exams",
      permission: "read-exam",
      onSelect: () => {
        open.value = false;
      },
    },
    {
      class: "text-lg",
      label: "الدرجات",
      icon: "i-lucide-pencil",
      to: "/results",
      permission: "read-result",
      onSelect: () => {
        open.value = false;
      },
    },
    {
      class: "text-lg",
      label: "المدفوعات",
      icon: "i-lucide-dollar-sign",
      to: "/payments",
      permission: "read-payment",
      onSelect: () => {
        open.value = false;
      },
    },
    {
      class: "text-lg",
      label: "الموظفين",
      icon: "i-lucide-users",
      to: "/users",
      permission: "read-user",
      onSelect: () => {
        open.value = false;
      },
    },
    {
      class: "text-lg",
      label: "الحضور والغياب",
      icon: "i-lucide-check-check",
      to: "/attendance",
      permission: "read-attendance",
      onSelect: () => {
        open.value = false;
      },
    },
  ],
  [
    {
      class: "text-lg",
      label: "الاعدادات",
      icon: "i-lucide-settings",
      to: "/settings",
      permission: "read-setting",
      onSelect: () => {
        open.value = false;
      },
    },
  ],
];

const groups = computed(() => [
  {
    id: "links",
    label: "Go to",
    items: links.flat(),
  },
  {
    id: "code",
    label: "Code",
    items: [
      {
        id: "source",
        label: "View page source",
        icon: "i-simple-icons-github",
        to: `https://github.com/nuxt-ui-pro/dashboard/blob/main/app/pages${
          route.path === "/" ? "/index" : route.path
        }.vue`,
        target: "_blank",
      },
    ],
  },
]);

const authStore = useAuthStore();
const items = ref([[], []]); // Two groups like links[0], links[1]

onMounted(() => {
  const permissions = authStore.permissions;

  // Reset both groups
  items.value = [[], []];

  links[0]?.forEach((item) => {
    if (permissions.includes(item.permission)) {
      items.value[0].push(item);
    }
  });

  links[1]?.forEach((item) => {
    if (permissions.includes(item.permission)) {
      items.value[1].push(item);
    }
  });
});
</script>

<template>
  <UDashboardGroup unit="rem">
    <UDashboardSidebar
      id="default"
      v-model:open="open"
      collapsible
      resizable
      class="bg-elevated/25"
      :ui="{ footer: 'lg:border-t lg:border-default' }"
    >
      <template #header="{ collapsed }">
        <TeamsMenu :collapsed="collapsed" />
      </template>

      <template #default="{ collapsed }">
        <!-- <UDashboardSearchButton :collapsed="collapsed" class="bg-transparent ring-default" /> -->

        <UNavigationMenu
          :collapsed="collapsed"
          :items="items[0]"
          orientation="vertical"
        />

        <UNavigationMenu
          :collapsed="collapsed"
          :items="items[1]"
          orientation="vertical"
          class="mt-auto"
        />
      </template>

      <template #footer="{ collapsed }">
        <UserMenu :collapsed="collapsed" />
      </template>
    </UDashboardSidebar>

    <UDashboardSearch :groups="groups" />

    <slot />

    <NotificationsSlideover />
  </UDashboardGroup>
</template>
