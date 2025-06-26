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
        class: "text-xl",
        label: "الصفحة الرئيسية",
        icon: "i-lucide-house",
        to: "/",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-xl",
        label: "المواد الدراسية",
        icon: "i-lucide-book-open",
        to: "/courses",
        permission: "read-course",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-xl",
        label: "المدرسيين",
        icon: "i-lucide-users",
        to: "/teachers",
        permission: "read-teacher",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-xl",
        label: "أولياء الامور",
        icon: "i-lucide-users",
        to: "/parents",
        permission: "read-parentmodel",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-xl",
        label: "الطلاب",
        icon: "i-heroicons-user-group",
        to: "/students",
        permission: "read-student",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-xl",
        label: "المجموعات",
        icon: "i-lucide-group",
        to: "/groups",
        permission: "read-group",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-xl",
        label: "الاختبارات",
        icon: "i-lucide-book-open",
        to: "/exams",
        permission: "read-exam",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-xl",
        label: "الدرجات",
        icon: "i-lucide-pencil",
        to: "/results",
        permission: "read-examresult",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-xl",
        label: "المدفوعات",
        icon: "i-lucide-dollar-sign",
        to: "/payments",
        permission: "read-payment",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-xl",
        label: "الحضور والغياب",
        icon: "i-lucide-check-check",
        to: "/attendance",
        permission: "read-attendance",
        onSelect: () => (open.value = false),
      },
      {
        label: "التقارير",
        icon: "i-lucide-bar-chart-2",
        class: "text-xl",
        permission: "read-report",
        defaultOpen: true,
        children: [
          {
            label: "تقارير  المجموعات",
            icon: "i-lucide-group",
            to: "/reports/groups",
            permission: "read-groupreport",
          },
          {
            label: "تقارير أداء الطلاب",
            icon: "i-lucide-users",
            to: "/reports/students",
            permission: "read-studentreport",
          },
          {
            label: "تقارير الحجز والحضور",
            icon: "i-lucide-check-check",
            to: "/reports/attendances",
            permission: "read-attendancereport",
          },
          {
            label: "تقارير المالية و المصاريف",
            icon: "i-lucide-dollar-sign",
            to: "/reports/payments",
            permission: "read-paymentreport",
          },
        ],
      },
    ],
    [
      {
        class: "text-xl",
        label: "الموظفين",
        icon: "i-lucide-users",
        to: "/users",
        permission: "read-user",
        onSelect: () => (open.value = false),
      },
      {
        class: "text-xl",
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
      if (
        permissions.includes(item.permission) ||
        item.permission == undefined
      ) {
        if (item?.children?.length > 0) {
          item?.children?.forEach((child) => {
            if (!permissions.includes(child?.permission)) {
              item?.children?.pop(child);
            }
          });
        }

        items.value[0].push(item);
      }
    });

    links[1]?.forEach((item) => {
      if (
        permissions.includes(item.permission) ||
        item.permission == undefined
      ) {
        if (item?.children) {
          item?.children?.forEach((child) => {
            if (!permissions.includes(child?.permission)) {
              item?.children?.pop(child);
            }
          });
        }
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
