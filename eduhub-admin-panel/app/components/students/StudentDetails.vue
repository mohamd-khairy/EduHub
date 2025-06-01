<script setup lang="ts">
import { format } from 'date-fns'
import type { Mail } from '~/types'

defineProps<{
  mail: Mail
}>()

const links = [[{
  label: 'General',
  icon: 'i-lucide-user',
  to: '/students',
  exact: true
}, {
  label: 'Members',
  icon: 'i-lucide-users',
  to: '/students/members'
}, {
  label: 'Notifications',
  icon: 'i-lucide-bell',
  to: '/students/notifications'
}, {
  label: 'Security',
  icon: 'i-lucide-shield',
  to: '/students/security'
}]]
</script>

<template>
  <UDashboardPanel id="inbox-2">
    <template #header>
      <UDashboardNavbar title="تفاصيل الطالب" :toggle="false">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <!-- NOTE: The `-mx-1` class is used to align with the `DashboardSidebarCollapse` button here. -->
        <UNavigationMenu :items="links" highlight class="-mx-1 flex-1" />
      </UDashboardToolbar>
    </template>

    <template #body>
      <div class="flex flex-col gap-4 sm:gap-6 lg:gap-12 w-full lg:max-w-2xl mx-auto">
        <NuxtPage />
      </div>
    </template>
  </UDashboardPanel>
</template>
