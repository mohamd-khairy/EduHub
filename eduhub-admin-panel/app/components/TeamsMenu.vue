<script setup lang="ts">
defineProps<{
  collapsed?: boolean
}>()
</script>

<template>
  <UButton
    color="neutral"
    variant="ghost"
    block
    :square="collapsed"
    class="items-center justify-start data-[state=open]:bg-elevated"
    :class="[!collapsed && 'py-2']"
    :ui="{
      trailingIcon: 'text-dimmed'
    }"
  >
    <!-- ✅ الشعار -->
    <img
      src="assets/images/logo.png"
      alt="شعار المسار"
      class="w-45 h-35 rounded-full ml-2"
    />

    <!-- ✅ النص يظهر فقط إذا لم يكن collapsed -->
    <!-- <span v-if="!collapsed">المَسار</span> -->
  </UButton>
</template>
