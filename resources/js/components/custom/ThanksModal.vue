<template>
  <div v-if="status == 'thanks'" class="w-full flex flex-col items-center justify-center animate__animated animate__zoomIn z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
      <h2 class="text-2xl font-bold mb-4">Thank you!</h2>
      <p class="mb-4">Your feedback is important to us. We're glad you had a positive experience.</p>
      <button @click="closeModal" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded">Close</button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    status: {
      type: String,
      required: true,
    },
  },
  methods: {
    closeModal() {
      window.location.reload();
    },
  },
};
</script>
