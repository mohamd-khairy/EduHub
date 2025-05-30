import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useGroupStore = defineStore('group', () => {
  const items = ref<object[]>([])
  const selectedIds = ref<number[]>([])
  const deleteModalOpen = ref(false)
  const idsToDelete = ref<number[]>([])

  // Pagination state — optional if you want to track for UI
  const pagination = ref({
    page: 1,
    pageCount: 1,
    pageSize: 10,
    total: 0
  })

  // Load all pages from backend, combine all items into one array
  async function loadAllGroups(page = 1) {
    items.value = [] // clear current items

    const res = await fetch(`http://localhost/EduHub/eduhub-backend/public/api/group?relations=teacher,course&page=${page}`)
    const json = await res.json()

    if (json?.data) {
      items.value = json.data.data

      // Update pagination info from last response
      pagination.value.page = json.data.current_page
      pagination.value.pageCount = json.data.last_page
      pagination.value.pageSize = json.data.per_page
      pagination.value.total = json.data.total
    }
  }

  // Toggle selection for a single ID
  function toggleId(id: number) {
    if (selectedIds.value.includes(id)) {
      selectedIds.value = selectedIds.value.filter(i => i !== id)
    } else {
      selectedIds.value.push(id)
    }
  }

  // Add an ID if not present
  function addId(id: number) {
    if (!selectedIds.value.includes(id)) {
      selectedIds.value.push(id)
    }
  }

  // Remove an ID if present
  function removeId(id: number) {
    selectedIds.value = selectedIds.value.filter(i => i !== id)
  }

  // Clear all selections
  function clearSelection() {
    selectedIds.value = []
  }

  // Delete selected groups from backend, then update local items and selection
  async function deleteSelectedGroups() {
    if (selectedIds.value.length === 0) return

    const res = await fetch('http://localhost/EduHub/eduhub-backend/public/api/group/delete-all', {
      method: 'POST', // Adjust method as your API requires
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ ids: selectedIds.value })
    })

    if (res.ok) {
      // Remove deleted items locally
      items.value = items.value.filter(item => !selectedIds.value.includes(item.id))
      deleteModalOpen.value = false
      clearSelection()
    } else {
      throw new Error('Failed to delete groups')
    }
  }

  return {
    items,
    selectedIds,
    deleteModalOpen,
    idsToDelete,
    pagination,
    loadAllGroups,
    toggleId,
    addId,
    removeId,
    clearSelection,
    deleteSelectedGroups
  }
})
