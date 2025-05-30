import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useCourseStore = defineStore('course', () => {
  const items = ref<object[]>([])
  const courseOptions = ref<object[]>([])
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
  async function loadAllCourses(page = 1) {
    items.value = [] // clear current items

    const res = await fetch(`http://localhost/EduHub/eduhub-backend/public/api/course?page=${page}`)
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

  async function loadCourses(search = null) {
    items.value = [] // clear current items

    const res = await fetch(`http://localhost/EduHub/eduhub-backend/public/api/course/all?search=${search}`)
    const json = await res.json()

    if (json?.data) {
      items.value = json.data
    }
  }

  async function loadCoursesForSelect(search = null) {
    try {
      const response = await fetch(`http://localhost/EduHub/eduhub-backend/public/api/course/all?search=${search || ''}`)

      if (!response.ok) {
        console.error('Failed to load courses:', response.statusText)
        return []
      }

      const json = await response.json()

      // Assuming the API response has a structure like { data: [...] }
      if (!json.data || !Array.isArray(json.data)) {
        console.error('Invalid data format received')
        return []
      }

      // Map data to the format expected by your select
      courseOptions.value = json.data.map((item: { id: number, name: string }) => ({
        label: item.name,
        value: String(item.id)
      }))
    } catch (error) {
      console.error('Error loading courses:', error)
      return []
    }
  }

  // Delete selected courses from backend, then update local items and selection
  async function deleteSelectedcourses() {
    if (selectedIds.value.length === 0) return

    const res = await fetch('http://localhost/EduHub/eduhub-backend/public/api/course/delete-all', {
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
      throw new Error('Failed to delete courses')
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

  return {
    items,
    courseOptions,
    selectedIds,
    deleteModalOpen,
    idsToDelete,
    pagination,
    loadAllCourses,
    loadCourses,
    loadCoursesForSelect,
    toggleId,
    addId,
    removeId,
    clearSelection,
    deleteSelectedcourses
  }
})
