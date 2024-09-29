<template>
  <div v-if="status == 'connecting'" class="flex flex-col items-center justify-center animate__animated animate__jackInTheBox">
    <div class="flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-10 w-full max-w-2xl">
        <p class="text-2xl text-gray-700 italic text-center mb-4">{{ message }}</p>
        <div class="spinner-border" role="status">
          <SeventyLoading />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SeventyLoading from "@/components/custom/SeventyLoading.vue";
export default {
  name: "ChatLoading",
  components: {
    SeventyLoading,
  },
  data() {
    return {
      message: "Finding a staff member...",
    };
  },
  props: {
    status: {
      type: String,
      default: "connecting",
    },
  },
  created() {
    setTimeout(() => {
      this.message = "Knocking on their door...";
    }, 1500);
    setTimeout(() => {
      this.message = "Entering the chat...";
    }, 3000);
  },
};
</script>
