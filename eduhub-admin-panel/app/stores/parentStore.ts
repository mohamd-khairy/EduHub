import { defineStore } from "pinia";
import { ref } from "vue";

export const useParentStore = defineStore("parent", () => {
  const api = useApi();

  const items = ref<object[]>([]);
  const parentOptions = ref<object[]>([]);
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
  async function loadAllParents(page = 1) {
    items.value = []; // clear current items

    const res = await api(`parentModel?relations=students&page=${page}`);
    const json = await res.json();

    if (json?.data) {
      items.value = json.data.data;

      // Update pagination info from last response
      pagination.value.page = json.data.current_page;
      pagination.value.pageCount = json.data.last_page;
      pagination.value.pageSize = json.data.per_page;
      pagination.value.total = json.data.total;
    }
  }

  async function loadParents(search = null) {
    items.value = []; // clear current items

    const res = await api(`parentModel/all?search=${search}`);
    const json = await res.json();

    if (json?.data) {
      items.value = json.data;
    }
  }

  async function loadParentsForSelect(search = null) {
    try {
      const response = await api(`parentModel/all?search=${search || ""}`);

      if (!response.ok) {
        console.error("Failed to load courses:", response.statusText);
        return [];
      }

      const json = await response.json();

      // Assuming the API response has a structure like { data: [...] }
      if (!json.data || !Array.isArray(json.data)) {
        console.error("Invalid data format received");
        return [];
      }

      // Map data to the format expected by your select
      parentOptions.value = json.data.map(
        (item: { id: number; name: string }) => ({
          label: item.name,
          value: String(item.id),
        })
      );
    } catch (error) {
      console.error("Error loading courses:", error);
      return [];
    }
  }

  async function addParent(data) {
    const res = await api(`parentModel`, {
      method: "POST",
      body: JSON.stringify(data),
    });

    if (res.ok) {
      await loadAllParents();
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  async function editParent(data, id) {
    const res = await api(`parentModel/${id}`, {
      method: "PUT",
      body: JSON.stringify(data),
    });

    if (res.ok) {
      await loadAllParents();
      editModalOpen.value = false;
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  // Delete selected Parents from backend, then update local items and selection
  async function deleteSelectedParents() {
    if (selectedIds.value.length === 0) return;

    const res = await api(`parentModel/delete-all`, {
      method: "POST", // Adjust method as your API requires
      body: JSON.stringify({ ids: selectedIds.value }),
    });

    if (res.ok) {
      // Remove deleted items locally
      items.value = items.value.filter(
        (item) => !selectedIds.value.includes(item.id)
      );
      await loadAllParents();
      deleteModalOpen.value = false;
      clearSelection();
    } else {
      throw new Error("Failed to delete Parents");
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
    parentOptions,
    selectedIds,
    deleteModalOpen,
    idsToDelete,
    pagination,
    editModalOpen,
    editItem,
    addParent,
    editParent,
    loadAllParents,
    loadParents,
    loadParentsForSelect,
    toggleId,
    addId,
    removeId,
    clearSelection,
    deleteSelectedParents,
  };
});
