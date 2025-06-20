<script setup lang="ts">
import { ref, reactive, watch, onMounted, computed } from "vue";
import { useRoleStore } from "~/stores/roleStore";
import groupBy from "lodash/groupBy";
import AddRoleModal from "~/components/settings/AddRoleModal.vue";

const roleStore = useRoleStore();
const permissionStore = usePermissionStore();

const roles = ref<any[]>([]);
const permissions = ref<any[]>([]);
const selectedRole = ref<any | null>(null);
const state = reactive<{ [key: string]: boolean }>({});
const search = ref("");
const isLoading = ref(true);

let debounceTimer: ReturnType<typeof setTimeout>;

function onChange(role: any) {
  permissions.value.forEach((p: any) => {
    state[p.name] = state[role.name];
  });
}

function onChangePermission(permission: any) {
  state[selectedRole.value.name] = permissions.value.every(
    (perm: any) => state[perm.name]
  );
}

function toggleGroup(group: string, value: boolean) {
  if (!groupedPermissions.value[group]) return;
  groupedPermissions.value[group].forEach((permission) => {
    state[permission.name] = value;
  });

  state[selectedRole.value.name] = permissions.value.every(
    (perm: any) => state[perm.name]
  );
}

function setFilterValue(value: string) {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    if (value?.length >= 3) {
      roles.value = roleStore.items.filter((r: any) =>
        r.name.toLowerCase().includes(value.toLowerCase())
      );
    } else {
      roles.value = roleStore.items;
    }
  }, 500);
}
const groupedPermissions = computed(() => {
  if (!permissions.value) return {};
  return groupBy(permissions.value, "group");
});

async function getRoles() {
  isLoading.value = true;
  await roleStore.loadRoles();
  roles.value = roleStore.items;
  isLoading.value = false;
}

onMounted(async () => {
  isLoading.value = true;
  getRoles();
  await permissionStore.loadPermissions();
  permissions.value = permissionStore.items;

  isLoading.value = false;
});

watch(selectedRole, (newVal) => {
  if (!newVal) return;
  Object.keys(state).forEach((key) => delete state[key]);
  permissions.value.forEach((p: any) => {
    state[p.name] = newVal.permissions.some(
      (perm: any) => perm.name === p.name
    );
  });

  state[selectedRole.value.name] = permissions.value.every(
    (perm: any) => state[perm.name]
  );
});

function formatPermissionName(name: string): string {
  return name.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
}

watch(state, async () => {
  const permission_ids = [];

  Object.keys(state)
    .filter((key) => state[key])
    .forEach((key) => {
      const exists = permissions.value.find((perm: any) => perm.name === key);

      if (exists) {
        permission_ids.push(exists.id);
      }
    });

  const payload = {
    permissions: permission_ids,
  };
  await roleStore.editRolePermission(payload, selectedRole.value.id);
});

async function onRoleDelete(role: any) {
  await roleStore.deleteRole(role.id);
}

watch(
  () => roleStore.items,
  () => {
    roles.value = roleStore.items;
  }
);

watch(
  () => roles.value,
  () => {
    if (roles.value.length > 0) {
      selectedRole.value = roles.value[0];
    }
  }
);
</script>

<template>
  <div class="flex h-screen w-full gap-4 px-4">
    <div
      v-if="isLoading"
      class="hidden lg:flex flex-col items-center justify-center flex-1 gap-4 text-center p-8"
    >
      <span
        class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-gray-900"
      ></span>
    </div>

    <template v-else>

      <!-- Right Sidebar: Role List -->
      <UCard class="w-1/4 p-0 overflow-y-auto">

        <template #header>
          <UInput
            icon="i-lucide-search"
            placeholder="ابحث ..."
            variant="outline"
            @input="(e) => setFilterValue(e.target.value)"
          />
        </template>

        <div class="divide-y divide-gray-200">
        <AddRoleModal v-if="selectedRole && roles.length > 0" class="mb-6"/>

          <UButton
            v-for="role in roles"
            :key="role.id"
            variant="ghost"
            color="gray"
            class="justify-between w-full text-left rounded-t-lg px-4 py-3"
            :class="
              selectedRole?.id === role.id ? 'bg-primary/10 text-primary' : ''
            "
            @click="selectedRole = role"
          >
            {{ role.name }}

            <UButton
              icon="i-lucide-trash"
              color="red"
              variant="soft"
              size="lg"
              class="hover:bg-red-100 hover:text-red-700 transition-colors duration-200"
              @click="onRoleDelete(role)"
            />
          </UButton>
        </div>
      </UCard>

      <!-- Left Side: Role Details -->
      <div class="flex-1 overflow-y-auto ">
        <UCard v-if="selectedRole && roles.length > 0" class="space-y-6">

          <template #header>
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-xl font-semibold">{{ selectedRole.name }}</h2>
                <p class="text-sm text-gray-500">
                  صلاحيات الدور واعداداته التفصيلية
                </p>
              </div>
              <USwitch
                v-model="state[selectedRole.name]"
                @update:model-value="() => onChange(selectedRole)"
              />
            </div>
          </template>

          <div
            v-if="Object.keys(groupedPermissions).length"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4"
          >
            <div
              v-for="(permissions, group) in groupedPermissions"
              :key="group"
              class="mb-6 border border-gray-200 rounded-lg p-4"
            >
              <div
                class="flex items-center justify-between px-2 py-2 mb-2 bg-gray-100 rounded-t-lg dark:bg-gray-500"
              >
                <USwitch
                  :model-value="permissions.every((p) => state[p.name])"
                  @update:model-value="(val) => toggleGroup(group, val)"
                />
                <h3 class="text-md font-semibold capitalize">{{ group }}</h3>
              </div>

              <div>
                <div
                  v-for="permission in permissions"
                  :key="permission.name"
                  class="flex items-center justify-between"
                >
                  <USwitch
                    v-model="state[permission.name]"
                    @update:model-value="onChangePermission(permission)"
                  />
                  <div class="text-right">
                    <p class="font-medium">
                      {{ formatPermissionName(permission.name) }}
                    </p>
                    <p class="text-sm text-gray-500">{{ permission.name }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </UCard>

        <UCard
          v-else
          class="lg:flex flex-col items-center justify-center flex-1 gap-4 text-center p-8"
        >
          <p>الرجاء اختيار دور من القائمة لعرض التفاصيل</p>
          <AddRoleModal />
        </UCard>
      </div>
    </template>
  </div>
</template>
