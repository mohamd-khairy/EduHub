<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { breakpointsTailwind } from "@vueuse/core";

const chatStore = useChatStore();
const mails = ref([]);

onMounted(async () => {
  await chatStore.loadAllChats();
  mails.value = chatStore.items;
  selectedMail.value = mails.value?.length > 0 ? mails.value[0] : null;
});

const tabItems = [
  {
    label: "الكل",
    value: "all",
  },
  {
    label: "غير مقروء",
    value: "unread",
  },
];
const selectedTab = ref("all");

// Filter mails based on the selected tab
const filteredMails = computed(() => {
  if (selectedTab.value == "unread") {
    return mails.value.filter((mail) => !mail?.last_message?.is_read);
  }

  return mails.value;
});

const selectedMail = ref({});

const breakpoints = useBreakpoints(breakpointsTailwind);
const isMobile = breakpoints.smaller("lg");
</script>

<template>
  <UDashboardPanel
    id="inbox-1"
    :default-size="25"
    :min-size="20"
    :max-size="30"
    resizable
  >
    <UDashboardNavbar title="الرسائل">
      <template #leading>
        <UDashboardSidebarCollapse />
      </template>
      <template #trailing>
        <UBadge :label="filteredMails.length" variant="subtle" />
      </template>

      <template #right>
        <UTabs
          v-model="selectedTab"
          :items="tabItems"
          :content="false"
          size="xs"
        />
      </template>
    </UDashboardNavbar>
    <InboxList v-model="selectedMail" :mails="filteredMails" />
  </UDashboardPanel>

  <InboxMail
    v-if="selectedMail"
    :mail="selectedMail"
    @close="selectedMail = null"
  />
  <div v-else class="hidden lg:flex flex-1 items-center justify-center">
    <UIcon name="i-lucide-inbox" class="size-32 text-dimmed" />
  </div>

  <ClientOnly>
    <USlideover v-if="isMobile" v-model:open="isMailPanelOpen">
      <template #content>
        <InboxMail
          v-if="selectedMail"
          :mail="selectedMail"
          @close="selectedMail = null"
        />
      </template>
    </USlideover>
  </ClientOnly>
</template>
