<script setup lang="ts">
const authStore = useAuthStore();
const items = ref([[], []]);

definePageMeta({
  permission: "read-setting",
});

const links = [
  [
    {
      label: "الصلاحيات",
      icon: "i-lucide-bell",
      to: "/settings/roles",
      permission: "read-role",
    },
    {
      label: "سجل الاستخدام",
      icon: "i-lucide-shield",
      to: "/settings/logs",
      permission: "read-audit",
    },
    {
      label: "General",
      icon: "i-lucide-user",
      to: "/settings",
      exact: true,
      permission: "read-setting",
    },
    {
      label: "Members",
      icon: "i-lucide-users",
      to: "/settings/members",
      permission: "read-setting",
    },
    {
      label: "Security",
      icon: "i-lucide-shield",
      to: "/settings/security",
      permission: "read-setting",
    },
  ],
  [
    {
      label: "Documentation",
      icon: "i-lucide-book-open",
      to: "https://ui.nuxt.com/getting-started/installation/pro/nuxt",
      target: "_blank",
      permission: "read-setting",
    },
    {
      label: "Buy now",
      icon: "i-lucide-shopping-cart",
      to: "https://ui.nuxt.com/pro/purchase",
      target: "_blank",
      permission: "read-setting",
    },
  ],
];

onMounted(() => {
  const permissions = authStore.permissions;

  // Reset both groups
  items.value = [[], []];

  links[0]?.forEach((item) => {
    if (permissions.includes(item?.permission)) {
      items.value[0].push(item);
    }
  });

  links[1]?.forEach((item) => {
    if (permissions.includes(item?.permission)) {
      items.value[1].push(item);
    }
  });
});
</script>

<template>
  <UDashboardPanel id="settings" :ui="{ body: 'lg:py-12' }">
    <template #header>
      <UDashboardNavbar title="الاعدادات">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <!-- NOTE: The `-mx-1` class is used to align with the `DashboardSidebarCollapse` button here. -->
        <UNavigationMenu :items="items" highlight class="-mx-1 flex-1" />
      </UDashboardToolbar>
    </template>

    <template #body>
      <div
        class="flex flex-col gap-4 sm:gap-6 lg:gap-12 w-full lg:max-w-7xl mx-auto"
      >
        <NuxtPage />
      </div>
    </template>
  </UDashboardPanel>
</template>
