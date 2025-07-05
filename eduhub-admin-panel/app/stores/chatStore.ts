import { defineStore } from "pinia";
import { ref } from "vue";

export const useChatStore = defineStore("chat", () => {
  const api = useApi();

  const items = ref<object[]>([]);
  const selectedIds = ref<number[]>([]);
  const deleteModalOpen = ref(false);
  const idsToDelete = ref<number[]>([]);
  const editModalOpen = ref(false);
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

    const res = await api(`chat?page=${page}&relations=sender,receiver,last_message`);
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

  return {
    items,
    selectedIds,
    deleteModalOpen,
    idsToDelete,
    pagination,
    editModalOpen,
    editItem,
    loadAllChats,
  };
});
