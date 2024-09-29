<template>
  <div v-if="status == 'rating'" class="flex flex-col items-center justify-center animate__animated animate__jackInTheBox">
    <div class="flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
        <h2 class="text-2xl font-bold mb-4">Rate the conversation</h2>
        <star-rating v-model="rating" @update:rating="setRating" />
        <textarea v-model="comments" placeholder="Any comments?" class="w-full mt-4 p-2 border border-gray-300 rounded"></textarea>
        <button @click="rateConversation" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded mt-4">Submit</button>
        <button @click="status = 'chat'" class="bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded mt-4 ml-4">Cancel</button>
      </div>
    </div>
  </div>
</template>

<script>
import StarRating from "vue-star-rating";
export default {
  name: "RatingModal",
  props: {
    status: {
      type: String,
      default: "rating",
    },
  },
  data() {
    return {
      rating: 0,
      comments: "",
    };
  },
  components: {
    StarRating,
  },
  methods: {
    setRating(rating) {
      this.rating = rating;
    },
    async rateConversation() {
      try {
        // Ensure comments have a value
        if (!this.comments) {
          this.comments = "None";
        }
        // Send the rating to the backend
        const response = await fetch("/api/chat", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          },
          body: JSON.stringify({ feedback: { rating: this.rating, comments: this.comments } }),
        });

        const data = await response.json();

        if (data.success) {
          this.$emit("update:status", "thanks");
        } else {
          console.log("Error:", data.error);
        }
      } catch (error) {
        console.error("Error:", error);
      }
    },
  },
};
</script>
