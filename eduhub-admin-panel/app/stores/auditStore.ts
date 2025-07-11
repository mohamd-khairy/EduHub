import { defineStore } from "pinia";
import { ref } from "vue";

export const useAuditStore = defineStore("audit", () => {
  const api = useApi();

  const items = ref<object[]>([]);
  const paymentOptions = ref<object[]>([]);
  const selectedIds = ref<number[]>([]);
  const deleteModalOpen = ref(false);
  const idsToDelete = ref<number[]>([]);
  const editModalOpen = ref(false);
  const editItem = ref({});
  const isItemLoading = ref(false);
  // Pagination state — optional if you want to track for UI
  const pagination = ref({
    page: 1,
    pageCount: 1,
    pageSize: 10,
    total: 0,
  });

  // Load all pages from backend, combine all items into one array
  async function loadAllAudits(page = 1) {
    isItemLoading.value = true;
    items.value = []; // clear current items

    const res = await api(`audit?page=${page}`);
    const json = await res.json();

    if (json?.data) {
      items.value = json?.data?.data;

      // Update pagination info from last response
      pagination.value.page = json.data.current_page;
      pagination.value.pageCount = json.data.last_page;
      pagination.value.pageSize = json.data.per_page;
      pagination.value.total = json.data.total;
    }

    isItemLoading.value = false;
  }

  // Delete selected payments from backend, then update local items and selection
  async function deleteSelectedpayments() {
    if (selectedIds.value.length === 0) return;

    const res = await api(`audit/delete-all`, {
      method: "POST",
      body: JSON.stringify({ ids: selectedIds.value }),
    });

    if (res.ok) {
      // Remove deleted items locally
      items.value = items.value.filter(
        (item) => !selectedIds.value.includes(item.id)
      );
      await loadAllAudits();
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
    paymentOptions,
    selectedIds,
    deleteModalOpen,
    idsToDelete,
    pagination,
    editModalOpen,
    editItem,
    isItemLoading,
    loadAllAudits,
    toggleId,
    addId,
    removeId,
    clearSelection,
    deleteSelectedpayments,
  };
});
