<script setup>
import DashboardHeader from "~/components/reports/DashboardHeader.vue";

// No specific script is required, this is purely a display component
</script>

<template>
  <UDashboardPanel id="home">
    <template #header>
      <DashboardHeader
        title="غير مصرح لك"
        :reset-signal="false"
        :range="null"
        :group_id="null"
        :student_id="null"
        :is-loading="false"
        :has-filter="false"
        :has-permission="false"
      />
    </template>
    <template #body>
      <!-- <div class="min-h-screen dark:bg-(--chip-dark) font-sans antialiased"> -->
      <div
        class="mt-16 dark:bg-(--chip-dark) flex items-center justify-center"
      >
        <div
          class="w-100 bg-white dark:bg-(--chip-dark) rounded-2xl shadow-lg overflow-hidden"
        >
          <!-- Illustration Section -->
          <div
              class="hidden lg:flex items-center justify-center bg-white dark:bg-gray-800 p-10"
            >
              <img
                src="assets/images/agency.png"
                alt="توضيح"
                class="w-full max-w-md object-contain max-h-80"
                @error="handleImageError"
              />
            </div>

          <div class="flex items-center justify-center">
            <div class="flex justify-center items-center w-full">
              <div variant="warning" class="p-8 text-center max-w-md w-full">
                <h2 class="text-2xl font-semibold text-red-600">
                  403 - غير مصرح به
                </h2>
                <p class="mt-4 text-lg">ليس لديك إذن للوصول إلى هذه الصفحة.</p>
                <div class="mt-6">
                  <RouterLink to="/">
                    <UButton size="xl" variant="outline" class="text-red-600">
                      العودة إلى الصفحة الرئيسية
                    </UButton>
                  </RouterLink>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- </div> -->
    </template>
  </UDashboardPanel>
</template>
