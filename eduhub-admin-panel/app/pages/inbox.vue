<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { breakpointsTailwind } from "@vueuse/core";
import AddModal from "~/components/inbox/AddModal.vue";
definePageMeta({
  permission: "read-chat",
});
const chatStore = useChatStore();
const mails = ref([]);
const selectedMail = defineModel<null>();

onMounted(async () => {
  await chatStore.loadAllChats();
  // selectedMail.value = mails.value?.length > 0 ? mails.value[0] : null;
});

watch(
  () => chatStore.items,
  () => {
    mails.value = chatStore.items;
  },
  { immediate: true, deep: true }
);

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
    return mails.value.filter((mail) => mail?.last_message?.is_read == false);
  }

  return mails.value;
});

const breakpoints = useBreakpoints(breakpointsTailwind);
const isMobile = breakpoints.smaller("lg");
</script>

<template>
  <UDashboardPanel
    id="inbox-1"
    :default-size="25"
    :min-size="20"
    :max-size="30"
    v-if="mails.length > 0"
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
  <div
    v-else
    class="hidden lg:flex flex-col items-center justify-center flex-1 gap-4 text-center p-8"
  >
    <!-- Icon -->
    <UIcon name="i-lucide-inbox" class="size-32 text-dimmed" />

    <!-- Description -->
    <div>
      <h2 class="text-xl font-semibold text-gray-600 mb-1">
        إضافة محادثة جديدة
      </h2>
      <p class="text-sm text-gray-500">يمكنك البدء الآن.</p>
    </div>

    <AddModal />
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
