<script setup lang="ts">
const route = useRoute()
const toast = useToast()

const open = ref(false)

const links = [[{
  class: 'text-lg',
  label: 'الصفحة الرئيسية',
  icon: 'i-lucide-house',
  to: '/',
  onSelect: () => {
    open.value = false
  },
}, {

  class: 'text-lg',
  label: 'الطلاب',
  icon: 'i-lucide-inbox',
  to: '/students',
  // badge: '4',
  onSelect: () => {
    open.value = false
  }
}, {

  class: 'text-lg',
  label: 'المجموعات',
  icon: 'i-lucide-inbox',
  to: '/groups',
  // badge: '4',
  onSelect: () => {
    open.value = false
  }
}, {

  class: 'text-lg',
  label: 'الكورسات',
  icon: 'i-lucide-users',
  to: '/courses',
  onSelect: () => {
    open.value = false
  }
}, {
  class: 'text-lg',
  label: 'المدرسيين',
  icon: 'i-lucide-users',
  to: '/teachers',
  onSelect: () => {
    open.value = false
  }
}, {
  class: 'text-lg',
  label: 'أولياء الامور',
  icon: 'i-lucide-users',
  to: '/parents',
  onSelect: () => {
    open.value = false
  }
}, {
  class: 'text-lg',
  label: 'الموظفين',
  icon: 'i-lucide-users',
  to: '/users',
  onSelect: () => {
    open.value = false
  }
}, {
  class: 'text-lg',
  label: 'المدفوعات',
  icon: 'i-lucide-users',
  to: '/payments',
  onSelect: () => {
    open.value = false
  }
}, {
  class: 'text-lg',
  label: 'الامتحانات',
  icon: 'i-lucide-users',
  to: '/exams',
  onSelect: () => {
    open.value = false
  }
}, {
  class: 'text-lg',
  label: 'الدرجات',
  icon: 'i-lucide-users',
  to: '/results',
  onSelect: () => {
    open.value = false
  }
}, {

  class: 'text-lg',
  label: 'الاعدادات',
  to: '/settings',
  icon: 'i-lucide-settings',
  defaultOpen: true,
  children: [{
    class: 'text-lg',
    label: 'الاعدادات العامة',
    to: '/settings',
    exact: true,
    onSelect: () => {
      open.value = false
    }
  }, {

    class: 'text-lg',
    label: 'الادوار',
    to: '/settings/members',
    onSelect: () => {
      open.value = false
    }
  }, {

    class: 'text-lg',
    label: 'الصلاحيات',
    to: '/settings/members',
    onSelect: () => {
      open.value = false
    }
  }, {
    class: 'text-lg',
    label: 'الاشعارات',
    to: '/settings/notifications',
    onSelect: () => {
      open.value = false
    }
  }, {
    class: 'text-lg',
    label: 'الحماية',
    to: '/settings/security',
    onSelect: () => {
      open.value = false
    }
  }]
}], [{
  class: 'text-lg',
  label: 'التقييمات',
  icon: 'i-lucide-message-circle',
  to: '',
  target: '_blank'
}, {

  class: 'text-lg',
  label: 'المساعدة',
  icon: 'i-lucide-info',
  to: '',
  target: '_blank'
}]]

const groups = computed(() => [{
  id: 'links',
  label: 'Go to',
  items: links.flat()
}, {
  id: 'code',
  label: 'Code',
  items: [{
    id: 'source',
    label: 'View page source',
    icon: 'i-simple-icons-github',
    to: `https://github.com/nuxt-ui-pro/dashboard/blob/main/app/pages${route.path === '/' ? '/index' : route.path}.vue`,
    target: '_blank'
  }]
}])

onMounted(async () => {
  const cookie = useCookie('cookie-consent')
  if (cookie.value === 'accepted') {
    return
  }

  toast.add({
    title: 'We use first-party cookies to enhance your experience on our website.',
    duration: 0,
    close: false,
    actions: [{
      label: 'Accept',
      color: 'neutral',
      variant: 'outline',
      onClick: () => {
        cookie.value = 'accepted'
      }
    }, {
      label: 'Opt out',
      color: 'neutral',
      variant: 'ghost'
    }]
  })
})
</script>

<template>
  <UDashboardGroup unit="rem">
    <UDashboardSidebar
      id="default"
      v-model:open="open"
      collapsible
      resizable
      class="bg-elevated/25"
      :ui="{ footer: 'lg:border-t lg:border-default' }">
      <template #header="{ collapsed }">
        <TeamsMenu :collapsed="collapsed" />
      </template>

      <template #default="{ collapsed }">
        <!-- <UDashboardSearchButton :collapsed="collapsed" class="bg-transparent ring-default" /> -->

        <UNavigationMenu :collapsed="collapsed" :items="links[0]" orientation="vertical" />

        <UNavigationMenu
          :collapsed="collapsed"
          :items="links[1]"
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
