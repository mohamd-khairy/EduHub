import { defineStore } from "pinia";
import { ref } from "vue";

export const useGroupStore = defineStore("group", () => {
  const api = useApi();

  const items = ref<object[]>([]);
  const groupsByTime = ref<object[]>([]);
  const groupOptions = ref<object[]>([]);
  const selectedIds = ref<number[]>([]);
  const deleteModalOpen = ref(false);
  const editModalOpen = ref(false);
  const showGroupStudentsModal = ref(false);
  const showGroupExamsModal = ref(false);
  const editItem = ref({});
  const selectedItem = ref({});
  const idsToDelete = ref<number[]>([]);
  const todayGroupAttendance = ref([]);
  const isLoading = ref(false);

  // Pagination state — optional if you want to track for UI
  const pagination = ref({
    page: 1,
    pageCount: 1,
    pageSize: 10,
    total: 0,
  });

  // Load all pages from backend, combine all items into one array
  async function loadAllGroups(page = 1) {
    items.value = []; // clear current items

    const res = await api(
      `group?relations=teacher,course,schedules,students.parent,exams,studyYear&page=${page}`
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

  async function loadAllGroupsByTime(date = null) {
    groupsByTime.value = []; // clear current items

    const res = await api(`group/groups-by-time?date=${date}`);
    const json = await res.json();

    if (json?.data) {
      groupsByTime.value = json.data;
    }
  }

  async function loadGroupTodayAttendance(data, date = null) {
    todayGroupAttendance.value = []; // Clear current items

    const res = await api(`group/group-attendance?date=${date}`, {
      method: "POST",
      body: JSON.stringify(data),
    });

    const json = await res.json();

    if (json?.data) {
      todayGroupAttendance.value = json.data;
    } else {
      console.error("Failed to load group attendance", json);
    }
  }

  async function loadGroupsForSelect(search = null) {
    try {
      let url = `group/all`;

      if (search) {
        url += `?search=${search || ""}`;
      }

      const response = await api(url);

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
      groupOptions.value = json.data;
    } catch (error) {
      console.error("Error loading courses:", error);
      return [];
    }
  }

  async function addGroup(data) {
    const res = await api(`group`, {
      method: "POST", // Adjust method as your API requires
      body: JSON.stringify(data),
    });

    if (res.ok) {
      await loadAllGroups();
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  async function editGroup(data, id) {
    const res = await api(`group/${id}`, {
      method: "PUT", // Adjust method as your API requires
      body: JSON.stringify(data),
    });

    if (res.ok) {
      await loadAllGroups();
      editModalOpen.value = false;
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  // Delete selected groups from backend, then update local items and selection
  async function deleteSelectedGroups() {
    if (selectedIds.value.length === 0) return;

    const res = await api(`group/delete-all`, {
      method: "POST", // Adjust method as your API requires
      body: JSON.stringify({ ids: selectedIds.value }),
    });

    if (res.ok) {
      // Remove deleted items locally
      items.value = items.value.filter(
        (item) => !selectedIds.value.includes(item.id)
      );
      await loadAllGroups();
      deleteModalOpen.value = false;
      clearSelection();
    } else {
      throw new Error("Failed to delete groups");
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
    editModalOpen,
    editItem,
    idsToDelete,
    pagination,
    groupOptions,
    selectedItem,
    showGroupStudentsModal,
    showGroupExamsModal,
    groupsByTime,
    todayGroupAttendance,
    isLoading,
    loadGroupTodayAttendance,
    loadAllGroupsByTime,
    loadGroupsForSelect,
    loadAllGroups,
    addGroup,
    editGroup,
    deleteSelectedGroups,
    toggleId,
    addId,
    removeId,
    clearSelection,
  };
});
