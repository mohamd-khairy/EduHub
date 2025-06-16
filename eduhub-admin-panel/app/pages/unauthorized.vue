<script setup>
// No specific script is required, this is purely a display component
</script>

<template>
  <div class="min-h-screen dark:bg-(--chip-dark) font-sans antialiased">
    <div
      class="min-h-screen dark:bg-(--chip-dark) flex items-center justify-center px-4"
    >
      <div
        class="lg:w-[60%] grid grid-cols-1 lg:grid-cols-2 gap-0 bg-white dark:bg-(--chip-dark) rounded-2xl shadow-lg overflow-hidden"
      >
        <!-- Illustration Section -->
        <div
          class="hidden lg:flex items-center justify-center bg-white dark:bg-gray-800 p-10"
        >
          <img
            src="assets/images/agency.png"
            alt="توضيح"
            class="w-full max-w-md object-contain"
            @error="handleImageError"
          />
        </div>

        <div class="flex items-center justify-center h-64">
          <div class="flex justify-center items-center w-full">
            <div variant="warning" class="p-8 text-center max-w-md w-full">
              <h2 class="text-2xl font-semibold text-red-600">
                403 - Unauthorized
              </h2>
              <p class="mt-4 text-lg">
                You do not have permission to access this page.
              </p>
              <div class="mt-6">
                <UButton to="/" variant="outline" class="text-red-600">
                  Go Back to Home
                </UButton>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
