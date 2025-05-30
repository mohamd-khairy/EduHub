import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useGroupStore = defineStore('group', () => {
  const selectedIds = ref<number[]>([])
  const items = ref<object[]>([])

  const pagination = ref({
    page: 1,
    pageCount: 1,
    pageSize: 10,
    total: 0
  })

  async function loadGroups(page = 1) {
    const res = await fetch(`http://localhost/EduHub/eduhub-backend/public/api/group?relations=teacher,course&page=${page}`)
    const json = await res.json()

    if (json?.data) {
      items.value = json.data.data

      pagination.value = {
        page: json.data.current_page,
        pageCount: json.data.last_page,
        pageSize: json.data.per_page,
        total: json.data.total
      }

      console.log(pagination.value);
      
    }
  }

  // Delete selected groups by IDs
  async function deleteSelectedGroups(ids = []) {
    if (ids.length === 0) return

    // Example: sending DELETE request with IDs as payload or query param
    const res = await fetch('http://localhost/EduHub/eduhub-backend/public/api/group/delete-all', {
      method: 'POST', // or DELETE depending on your API
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ ids }),
    })

    if (res.ok) {
      // Remove deleted items from local list
      items.value = items.value.filter(item => !ids.includes(item.id))
      // loadGroups()
    } else {
      // Handle error
      throw new Error('Failed to delete groups')
    }
  }

  return {
    selectedIds,
    items,
    pagination,
    loadGroups,
    deleteSelectedGroups,
  }
})
