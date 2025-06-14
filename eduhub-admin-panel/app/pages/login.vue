<template>
  <div class="min-h-screen bg-gray-50 dark:bg-(--chip-dark) font-sans antialiased">
    <div
      class="min-h-screen bg-gray-50 dark:bg-(--chip-dark) flex items-center justify-center px-4"
    >
      <div
        class="lg:w-[50%] grid grid-cols-1 lg:grid-cols-2 gap-0 bg-white dark:bg-(--chip-dark) rounded-2xl shadow-lg overflow-hidden"
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

        <!-- Login Form Section -->
        <div
          class="flex flex-col justify-center px-6 sm:px-10 md:px-14 py-16 bg-gray-50 dark:bg-(--ui-bg-elevated) text-right"
        >
          <div class="mb-10">
            <h1
              class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-3"
            >
              👋 مرحباً بك
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-base md:text-lg">
              الرجاء إدخال بيانات حسابك لتسجيل الدخول
            </p>
          </div>

          <UForm :state="form" @submit="onLogin" v-slot="{ submit }" class="space-y-6">
            <div
              class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400"
            >
              <UFormGroup label="البريد الإلكتروني" name="email" class="w-full">
                <UInput
                  v-model="form.email"
                  icon="i-lucide-mail"
                  placeholder="example@email.com"
                  type="email"
                  size="lg"
                  variant="outline"
                  class="w-full"
                />
              </UFormGroup>
            </div>
            <div
              class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400"
            >
              <UFormGroup label="كلمة المرور" name="password" class="w-full">
                <UInput
                  v-model="form.password"
                  icon="i-lucide-lock"
                  placeholder="••••••••"
                  type="password"
                  size="lg"
                  variant="outline"
                  class="w-full"
                />
              </UFormGroup>
            </div>
            <div
              class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400"
            >
              <label class="flex items-center gap-2">
                <UCheckbox />
                <span>تذكرني</span>
              </label>
              <NuxtLink to="#" class="text-yellow-600 hover:underline">
                نسيت كلمة المرور؟
              </NuxtLink>
            </div>

            <UButton
              block
              size="lg"
              color="primary"
              class="font-semibold text-lg py-3"
              type="submit"
              :disabled="loading"
              @click="submit"
            >
              <template #default>
                {{ loading ? "جاري الدخول..." : "تسجيل الدخول" }}
              </template>
            </UButton>
          </UForm>

          <!-- <p
            class="text-center text-base text-gray-600 dark:text-gray-400 mt-10"
          >
            ليس لديك حساب؟
            <NuxtLink
              to="#"
              class="text-yellow-600 hover:underline font-semibold"
            >
              أنشئ حسابًا
            </NuxtLink>
          </p> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

definePageMeta({ layout: "auth" });

const authStore = useAuthStore();
const toast = useToast();
const router = useRouter();

const form = ref({
  email: "",
  password: "",
});

const handleImageError = (event) => {
  event.target.onerror = null;
  event.target.src =
    "https://placehold.co/500x500/FFF8E1/E6B34A?text=Illustration";
};

const loading = ref(false);

const onLogin = async () => {
  loading.value = true;
  try {
    await authStore.login(form.value);

    if (authStore.token && authStore.user) {
      toast.add({
        title: "تم الدخول",
        description: "تم تسجيل الدخول بنجاح",
        color: "success",
      });
      router.push("/");
    } else {
      throw new Error("فشل تسجيل الدخول");
    }
  } catch (error) {
    toast.add({
      title: "خطأ",
      description: error?.message || "حدث خطأ غير متوقع",
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped></style>
