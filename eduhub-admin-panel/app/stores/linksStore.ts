import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { useAuthStore } from "@/stores/authStore"; // Assuming authStore manages user data and permissions
import { useRoute } from "vue-router"; // Import useRoute for route handling

export const useLinksStore = defineStore("links", () => {
  const authStore = useAuthStore(); // Accessing the auth store for user permissions and data
  const open = ref(false);
  const route = useRoute();

  // Initial links data (with permissions)
  const links = [
    [
      {
        class: "text-lg",
        label: "الصفحة الرئيسية",
        icon: "i-lucide-house",
        to: "/",
        permission: "read-dashboard",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-lg",
        label: "المواد الدراسية",
        icon: "i-lucide-book-open",
        to: "/courses",
        permission: "read-course",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-lg",
        label: "المدرسيين",
        icon: "i-lucide-users",
        to: "/teachers",
        permission: "read-teacher",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-lg",
        label: "أولياء الامور",
        icon: "i-lucide-users",
        to: "/parents",
        permission: "read-parent",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-lg",
        label: "الطلاب",
        icon: "i-heroicons-user-group",
        to: "/students",
        permission: "read-student",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-lg",
        label: "المجموعات",
        icon: "i-lucide-group",
        to: "/groups",
        permission: "read-group",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-lg",
        label: "الاختبارات",
        icon: "i-lucide-book-open",
        to: "/exams",
        permission: "read-exam",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-lg",
        label: "الدرجات",
        icon: "i-lucide-pencil",
        to: "/results",
        permission: "read-result",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-lg",
        label: "المدفوعات",
        icon: "i-lucide-dollar-sign",
        to: "/payments",
        permission: "read-payment",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-lg",
        label: "الموظفين",
        icon: "i-lucide-users",
        to: "/users",
        permission: "read-user",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-lg",
        label: "الحضور والغياب",
        icon: "i-lucide-check-check",
        to: "/attendance",
        permission: "read-attendance",
        onSelect: () => (open.value = false),
      },
    ],
    [
      {
        class: "text-lg",
        label: "الاعدادات",
        icon: "i-lucide-settings",
        to: "/settings/roles",
        permission: "read-setting",
        onSelect: () => (open.value = false),
      },
    ],
  ];

  // The 'items' will store the filtered links
  const items = ref([[], []]);

  // Action to filter links based on user permissions
  const filterLinks = () => {
    if (!process.client) return; // Ensure we're running on the client

    // Wait for authStore to load user permissions if it's not available
    if (!authStore.permissions.length) {
      authStore.loadUserData(); // Load the user data and permissions if not already loaded
    }

    // Get permissions from authStore
    const permissions = authStore.permissions;

    // Reset items (clear any previously filtered items)
    items.value = [[], []];

    // Filter links based on the permissions
    links[0]?.forEach((item) => {
      if (permissions.includes(item.permission)) {
        items.value[0].push(item);
      }
    });

    links[1]?.forEach((item) => {
      if (permissions.includes(item.permission)) {
        items.value[1].push(item);
      }
    });
  };

  // Computed property for groups
  const groups = computed(() => [
    { id: "links", label: "Go to", items: items.value.flat() }, // Use filtered items
    {
      id: "code",
      label: "Code",
      items: [
        {
          id: "source",
          label: "View page source",
          icon: "i-simple-icons-github",
          to: `https://github.com/nuxt-ui-pro/dashboard/blob/main/app/pages${
            route.path === "/" ? "/index" : route.path
          }.vue`,
          target: "_blank",
        },
      ],
    },
  ]);

  // Call filterLinks when the component is mounted (or whenever permissions change)
  filterLinks();

  // Return all the necessary states and actions
  return { links, items, groups, filterLinks, open };
});
