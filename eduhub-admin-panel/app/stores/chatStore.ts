import { defineStore } from "pinia";
import { ref } from "vue";

export const useChatStore = defineStore("chat", () => {
  const api = useApi();

  const items = ref<object[]>([]);
  const item_messages = ref<object[]>([]);
  const selectedIds = ref<number[]>([]);
  const deleteModalOpen = ref(false);
  const idsToDelete = ref<number[]>([]);
  const editModalOpen = ref(false);
  const isLoadingMessages = ref(false);
  const editItem = ref({});

  // Pagination state — optional if you want to track for UI
  const pagination = ref({
    page: 1,
    pageCount: 1,
    pageSize: 10,
    total: 0,
  });

  // Load all pages from backend, combine all items into one array
  async function loadAllChats(page = 1) {
    items.value = []; // clear current items

    const res = await api(
      `chat?page=${page}&per_page=20&relations=last_message`
    );
    const json = await res.json();

    if (json?.data) {
      items.value = json?.data?.data;

      // Update pagination info from last response
      pagination.value.page = json.data.current_page;
      pagination.value.pageCount = json.data.last_page;
      pagination.value.pageSize = json.data.per_page;
      pagination.value.total = json.data.total;
    }
  }

  async function loadAllMessages(page = 1, chatId: number) {
    if (!chatId) return;
    isLoadingMessages.value = true;
    item_messages.value = []; // clear current items

    const res = await api(`chatMessage?page=${page}&chat_id=${chatId}`);
    const json = await res.json();

    if (json?.data) {
      item_messages.value = json?.data?.data;
    }

    isLoadingMessages.value = false;
  }

  async function sendMessage(data: object) {
    const res = await api(`chatMessage`, {
      method: "POST",
      body: JSON.stringify(data),
    });

    if (res.ok) {
      // await loadAllChats();
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  async function markAsRead(id: number) {
    const res = await api(`chat/message-read/${id}`, {
      method: "POST",
    });

    if (res.ok) {
      // await loadAllChats();
    } else {
      throw new Error("Failed to mark as read");
    }
  }

  async function markAllAsRead(id: number) {
    const res = await api(`chat/message-read-all/${id}`, {
      method: "POST",
    });

    if (res.ok) {
      // await loadAllChats();
    } else {
      throw new Error("Failed to mark as read");
    }
  }

  return {
    items,
    item_messages,
    selectedIds,
    deleteModalOpen,
    idsToDelete,
    pagination,
    editModalOpen,
    editItem,
    isLoadingMessages,
    loadAllChats,
    loadAllMessages,
    markAsRead,
    markAllAsRead,
    sendMessage,
  };
});
