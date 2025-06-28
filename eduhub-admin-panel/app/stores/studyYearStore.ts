import { defineStore } from "pinia";
import { ref } from "vue";

export const useStudyYearStore = defineStore("studyYear", () => {
  const api = useApi();

  const items = ref<object[]>([]);
  const studyYearOptions = ref<object[]>([]);
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
  async function loadAllStudyYears(page = 1) {
    items.value = []; // clear current items

    const res = await api(`studyYear?page=${page}`);
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

  async function loadStudyYears(search = null) {
    items.value = []; // clear current items

    const res = await api(`studyYear/all?search=${search}`);
    const json = await res.json();

    if (json?.data) {
      items.value = json.data;
    }
  }

  async function addStudyYear(data) {
    const res = await api(`studyYear`, {
      method: "POST",
      body: JSON.stringify(data),
    });

    if (res.ok) {
      await loadAllStudyYears();
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  async function editStudyYear(data, id) {
    const res = await api(`studyYear/${id}`, {
      method: "PUT",
      body: JSON.stringify(data),
    });

    if (res.ok) {
      await loadAllStudyYears();
      editModalOpen.value = false;
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  // Delete selected studyYears from backend, then update local items and selection
  async function deleteSelectedstudyYears() {
    if (selectedIds.value.length === 0) return;

    const res = await api(`studyYear/delete-all`, {
      method: "POST", // Adjust method as your API requires
      body: JSON.stringify({ ids: selectedIds.value }),
    });

    if (res.ok) {
      // Remove deleted items locally
      items.value = items.value.filter(
        (item) => !selectedIds.value.includes(item.id)
      );
      await loadAllStudyYears();
      deleteModalOpen.value = false;
      clearSelection();
    } else {
      throw new Error("Failed to delete studyYears");
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
    studyYearOptions,
    selectedIds,
    deleteModalOpen,
    idsToDelete,
    pagination,
    editModalOpen,
    editItem,
    editStudyYear,
    addStudyYear,
    loadAllStudyYears,
    loadStudyYears,
    toggleId,
    addId,
    removeId,
    clearSelection,
    deleteSelectedstudyYears,
  };
});
