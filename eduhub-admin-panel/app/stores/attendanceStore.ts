import { defineStore } from "pinia";
import { ref } from "vue";

export const useAttendanceStore = defineStore("attendance", () => {
  const api = useApi();

  const items = ref<object[]>([]);
  const attendanceOptions = ref<object[]>([]);
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
  async function loadAllAttendances(page = 1) {
    items.value = []; // clear current items

    const res = await api(`attendance?relations=student&page=${page}`);
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

  async function loadAttendances(search = null) {
    items.value = []; // clear current items

    const res = await api(`attendance/all?search=${search}`);
    const json = await res.json();

    if (json?.data) {
      items.value = json.data;
    }
  }

  async function loadAttendancesForSelect(search = null) {
    try {
      const response = await api(`attendance/all?search=${search || ""}`);

      if (!response.ok) {
        console.error("Failed to load attendances:", response.statusText);
        return [];
      }

      const json = await response.json();

      // Assuming the API response has a structure like { data: [...] }
      if (!json.data || !Array.isArray(json.data)) {
        console.error("Invalid data format received");
        return [];
      }

      // Map data to the format expected by your select
      attendanceOptions.value = json.data.map(
        (item: { id: number; name: string }) => ({
          label: item.name,
          value: String(item.id),
        })
      );
    } catch (error) {
      console.error("Error loading attendances:", error);
      return [];
    }
  }

  async function updateAllAttendance(data) {
    const res = await api(`attendance/update-all-attendance`, {
      method: "POST",
      body: JSON.stringify(data),
    });

    if (res.ok) {
      // await loadAllAttendances();
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  async function addAttendance(data) {
    const res = await api(`attendance`, {
      method: "POST",
      body: JSON.stringify(data),
    });

    if (res.ok) {
      // await loadAllAttendances();
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  async function editAttendance(data, id) {
    const res = await api(`attendance/${id}`, {
      method: "PUT",
      body: JSON.stringify(data),
    });

    if (res.ok) {
      await loadAllAttendances();
      editModalOpen.value = false;
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  // Delete selected attendances from backend, then update local items and selection
  async function deleteSelectedattendances() {
    if (selectedIds.value.length === 0) return;

    const res = await api(`attendance/delete-all`, {
      method: "POST", // Adjust method as your API requires
      body: JSON.stringify({ ids: selectedIds.value }),
    });

    if (res.ok) {
      // Remove deleted items locally
      items.value = items.value.filter(
        (item) => !selectedIds.value.includes(item.id)
      );
      await loadAllAttendances();
      deleteModalOpen.value = false;
      clearSelection();
    } else {
      throw new Error("Failed to delete attendances");
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
    attendanceOptions,
    selectedIds,
    deleteModalOpen,
    idsToDelete,
    pagination,
    editModalOpen,
    editItem,
    updateAllAttendance,
    editAttendance,
    addAttendance,
    loadAllAttendances,
    loadAttendances,
    loadAttendancesForSelect,
    toggleId,
    addId,
    removeId,
    clearSelection,
    deleteSelectedattendances,
  };
});
