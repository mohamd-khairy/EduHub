import { defineStore } from "pinia";
import { ref } from "vue";

export const useRoleStore = defineStore("role", () => {
  const BASE_URL =
    import.meta.env.NUXT_API_BASE_URL ||
    "http://localhost/EduHub/eduhub-backend/public/api";
  const items = ref<object[]>([]);
  const roleOptions = ref<object[]>([]);
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
  async function loadAllRoles(page = 1) {
    items.value = []; // clear current items

    const res = await fetch(`${BASE_URL}/role?page=${page}`);
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

  async function loadRoles(search = null) {
    items.value = []; // clear current items

    const res = await fetch(
      `${BASE_URL}/role/all?limit=all&relations=permissions`
    );
    const json = await res.json();

    if (json?.data) {
      items.value = json.data;      
    }
  }

  async function loadRolesForSelect(search = null) {
    try {
      const response = await fetch(
        `${BASE_URL}/role/all?search=${search || ""}`
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
      roleOptions.value = json.data.map(
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

  async function addRole(data) {
    const res = await fetch(`${BASE_URL}/role`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (res.ok) {
      await loadRoles();
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  async function editRole(data, id) {
    const res = await fetch(`${BASE_URL}/role/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (res.ok) {
      await loadAllRoles();
      editModalOpen.value = false;
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  async function editRolePermission(data, id) {
    const res = await fetch(`${BASE_URL}/role/${id}/permissions`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (res.ok) {
    } else {
      throw new Error("Failed to delete groups");
    }
  }

  // Delete selected Roles from backend, then update local items and selection
  async function deleteSelectedRoles() {
    if (selectedIds.value.length === 0) return;

    const res = await fetch(`${BASE_URL}/role/delete-all`, {
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
      await loadAllRoles();
      deleteModalOpen.value = false;
      clearSelection();
    } else {
      throw new Error("Failed to delete Roles");
    }
  }

  async function deleteRole(id) {
    const res = await fetch(`${BASE_URL}/role/${id}`, {
      method: "DELETE",
    });

    if (res.ok) {
      await loadRoles();
    } else {
      throw new Error("Failed to delete Roles");
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
    roleOptions,
    selectedIds,
    deleteModalOpen,
    idsToDelete,
    pagination,
    editModalOpen,
    editItem,
    deleteRole,
    editRolePermission,
    addRole,
    editRole,
    loadAllRoles,
    loadRoles,
    loadRolesForSelect,
    toggleId,
    addId,
    removeId,
    clearSelection,
    deleteSelectedRoles,
  };
});
