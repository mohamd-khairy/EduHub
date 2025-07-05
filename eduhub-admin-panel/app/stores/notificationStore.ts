import { defineStore } from "pinia";
import { ref } from "vue";

export const useNotificationStore = defineStore("notification", () => {
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
  async function loadAllNotifications(page = 1) {
    items.value = []; // clear current items

    const res = await api(`notification?page=${page}`);
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

  async function markAsRead(id: number) {
    const res = await api(`notification/read/${id}`, {
      method: "POST",
    });

    if (res.ok) {
      await loadAllNotifications();
    } else {
      throw new Error("Failed to mark as read");
    }
  }

  // Delete selected payments from backend, then update local items and selection
  async function deleteSelectedNotifications() {
    if (selectedIds.value.length === 0) return;

    const res = await api(`notification/delete-all`, {
      method: "POST",
      body: JSON.stringify({ ids: selectedIds.value }),
    });

    if (res.ok) {
      // Remove deleted items locally
      items.value = items.value.filter(
        (item) => !selectedIds.value.includes(item.id)
      );
      await loadAllNotifications();
      deleteModalOpen.value = false;
      clearSelection();
    } else {
      throw new Error("Failed to delete payments");
    }
  }

  // Toggle selection for a single ID
  function toggleId(id: number) {
    if (selectedIds.value.includes(id)) {
      selectedIds.value = selectedIds.value.filter((i) => i !== id);
    } else {
      selectedIds.value.push(id);
    }
  }

  // Add an ID if not present
  function addId(id: number) {
    if (!selectedIds.value.includes(id)) {
      selectedIds.value.push(id);
    }
  }

  // Remove an ID if present
  function removeId(id: number) {
    selectedIds.value = selectedIds.value.filter((i) => i !== id);
  }

  // Clear all selections
  function clearSelection() {
    selectedIds.value = [];
  }

  return {
    items,
    selectedIds,
    deleteModalOpen,
    idsToDelete,
    pagination,
    editModalOpen,
    editItem,
    loadAllNotifications,
    markAsRead,
    toggleId,
    addId,
    removeId,
    clearSelection,
    deleteSelectedNotifications,
  };
});
