import { defineStore } from "pinia";
import { ref } from "vue";

export const useStudentStore = defineStore("student", () => {
  const BASE_URL =
    import.meta.env.NUXT_API_BASE_URL ||
    "http://localhost/EduHub/eduhub-backend/public/api";
  const items = ref<object[]>([]);
  const studentOptions = ref<object[]>([]);
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
  async function loadAllStudents(page = 1, params = null, search = null) {
    items.value = []; // clear current items

    const relations = "parent,groups.teacher,groups.course";

    if (params || search) {
      page = 1; // Reset to first page if params or search are provided
    }

    let url = `${BASE_URL}/student?page=${page}`;

    if (relations) {
      url += `&relations=${relations}`;
    }

    if (params) {
      const filterEntries = Object.entries(params).map(
        ([key, value]) => `[${key},${value}]`
      );
      const filterParam = `filters=[${filterEntries.join(",")}]`;
      url += `&${filterParam}`;
    }

    if (search) {
      const filterEntries = Object.entries(search).map(
        ([key, value]) => `[${key},${value}]`
      );
      const filterParam = `search=[${filterEntries.join(",")}]`;
      url += `&${filterParam}`;
    }

    const res = await fetch(url);
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

  async function loadStudents(search = null) {
    items.value = []; // clear current items

    const res = await fetch(`${BASE_URL}/student/all?search=${search}`);
    const json = await res.json();

    if (json?.data) {
      items.value = json.data;
    }
  }

  async function loadStudentsForSelect(search = null) {
    try {
      const response = await fetch(
        `${BASE_URL}/student/all?search=${search || ""}`
      );

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
      studentOptions.value = json.data.map(
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

  async function addStudent(data) {
    const res = await fetch(`${BASE_URL}/student`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (res.ok) {
      await loadAllStudents();
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  async function editStudent(data, id) {
    const res = await fetch(`${BASE_URL}/student/${id}`, {
      method: "POST",
      body: data,
    });

    if (res.ok) {
      await loadAllStudents();
      editModalOpen.value = false;
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  // Delete selected Students from backend, then update local items and selection
  async function deleteSelectedStudents() {
    if (selectedIds.value.length === 0) return;

    const res = await fetch(`${BASE_URL}/student/delete-all`, {
      method: "POST", // Adjust method as your API requires
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ ids: selectedIds.value }),
    });

    if (res.ok) {
      // Remove deleted items locally
      items.value = items.value.filter(
        (item) => !selectedIds.value.includes(item.id)
      );
      await loadAllStudents();
      deleteModalOpen.value = false;
      clearSelection();
    } else {
      throw new Error("Failed to delete Students");
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
    studentOptions,
    selectedIds,
    deleteModalOpen,
    idsToDelete,
    pagination,
    editModalOpen,
    editItem,
    addStudent,
    editStudent,
    loadAllStudents,
    loadStudents,
    loadStudentsForSelect,
    toggleId,
    addId,
    removeId,
    clearSelection,
    deleteSelectedStudents,
  };
});
