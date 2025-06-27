<script setup lang="ts">
defineProps<{
  collapsed?: boolean;
}>();
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
      trailingIcon: 'text-dimmed',
    }"
  >
    <!-- ✅ الشعار -->
    <img
      v-if="!collapsed"
      src="assets/images/logo.png"
      alt="شعار المسار"
      class="w-45 h-35 rounded-full ml-2"
    />

    <img
      v-else
      src="assets/images/samll-logo.png"
      alt="شعار المسار"
      class="h-10 rounded-full"
      style="max-width: 40px;margin-right: -10px;"
    />

    <!-- ✅ النص يظهر فقط إذا لم يكن collapsed -->
    <!-- <span v-else>المَسار</span> -->
  </UButton>
</template>
