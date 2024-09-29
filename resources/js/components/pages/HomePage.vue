<template>
  <!-- Connecting to the chat -->
  <ChatLoading :status="status" />

  <!-- Rating Modal-->
  <RatingModal :status="status" @update:status="handleStatusUpdate" />

  <!-- Thanks Modal -->
  <ThanksModal :status="status" />

  <!-- Chat Box -->
  <div v-if="status == 'chat'" class="w-full flex flex-col items-center justify-center animate__animated animate__zoomIn z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
      <div>
        <div class="flex justify-center mb-6" title="Tooltip directive content">
          <img src="https://seventytwo.nz/img/site/logos/seventytwo-bl.svg" alt="SeventyTwo Logo" class="h-16" />
        </div>

        <div id="chat-box" ref="chatBox" class="border border-gray-300 rounded-lg p-4 h-screen max-h-80 overflow-y-scroll bg-gray-50 mb-4">
          <p v-for="(msg, index) in messages" :key="index" :class="msg.class">
            <strong>{{ msg.author }}: </strong>
            <span v-html="msg.content"></span>
          </p>
        </div>

        <!-- Input and Send Button -->
        <div class="flex justify-center">
          <input v-show="complete == false" type="text" v-model="message" @keydown.enter="sendMessage" class="w-full border border-gray-300 text-black rounded-l-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" :placeholder="placeholder" />
          <button v-show="complete == false" @click="sendMessage" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded-r-lg">Send</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ChatLoading from "@/components/custom/ChatLoading.vue";
import RatingModal from "@/components/custom/RatingModal.vue";
import ThanksModal from "@/components/custom/ThanksModal.vue";

export default {
  data() {
    return {
      placeholder: "Enter your name...",
      message: "",
      messages: [
        {
          author: "Staff",
          content: "Hi there, could I please have your name?",
          class: "chat-bubble text-left bg-blue-600 text-white px-4 py-3 rounded mb-4 mt-4 max-w-max animate__animated animate__slideInLeft animate_faster",
        },
      ],
      complete: false,
      rating: 0,
      comments: "",
      status: "connecting",
    };
  },
  updated() {
    this.scrollToBottom();
  },
  components: {
    ChatLoading,
    RatingModal,
    ThanksModal,
  },
  created() {
    setTimeout(() => {
      this.status = "chat";
    }, 4500);
  },
  methods: {
    scrollToBottom() {
      const chatBox = this.$refs.chatBox;
      if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
      }
    },
    setRating(rating) {
      this.rating = rating;
    },
    async sendMessage() {
      try {
        if (!this.message) return;

        const userMessage = this.message;

        // Now clear the inputfield
        this.message = "";
        this.placeholder = "Enter your message...";

        // Add user's message to chat
        this.messages.push({
          author: "You",
          content: userMessage,
          class: "chat-bubble text-right bg-gray-600 text-white px-4 py-3 rounded mb-4 max-w-max ml-auto animate__animated animate__slideInRight animate_faster",
        });

        //1 second promise delay - animation purposes for demo
        await new Promise((resolve) => setTimeout(resolve, 1500));

        if (userMessage.toLocaleLowerCase() == "bye") {
          this.messages.push({
            author: "Staff",
            content: "Bye! Have a great day!",
            class: "chat-bubble text-left bg-blue-600 text-white px-4 py-3 rounded mb-4 max-w-max animate__animated animate__slideInLeft animate_faster",
          });
          await new Promise((resolve) => setTimeout(resolve, 1500));
          this.status = "rating";
          return;
        }

        // Create a typing indicator for the staff
        const typingMessage = {
          author: "Staff",
          content: '<span class="typing-dots"><div></div><div></div><div></div></span>',
          class: "chat-bubble text-left text-white justify-center align-center bg-blue-600 px-4 py-3 rounded mb-4 max-w-max animate__animated animate__slideInLeft animate_faster",
        };
        this.messages.push(typingMessage);

        // Send the message to the backend
        const response = await fetch("/api/chat", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          },
          body: JSON.stringify({ message: userMessage }),
        });

        const data = await response.json();

        // Remove typing animation and replace with staff reply
        this.messages.pop();
        this.messages.push({
          author: "Staff",
          content: data.reply,
          class: "chat-bubble text-left bg-blue-600 text-white px-4 py-3 rounded mb-4 max-w-max animate__animated animate__slideInLeft animate_faster",
        });
      } catch (error) {
        console.error("Error:", error);
      }
    },
    handleStatusUpdate(newStatus) {
      this.status = newStatus;
    },
  },
};
</script>
