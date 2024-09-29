<template>
  <!-- loading -->
  <div v-if="loading" class="relative flex justify-center items-center w-1/2 h-screen">
    <img src="https://seventytwo.nz/img/site/logos/seventytwo-bl.svg" class="animate-ping absolute inline-flex w-1/2 h-full opacity-75" />
    <img src="https://seventytwo.nz/img/site/logos/seventytwo-bl.svg" class="w-1/2 relative inline-flex" />
  </div>

  <div v-show="!loading" class="w-11/12 flex flex-col items-center justify-center z-50 py-10 lg:py-20 overflow-scroll">
    <div class="grid grid-cols-1 gap-8 w-10/12 overflow-y-scroll lg:grid-cols-3">
      <div v-if="users.length > 0" class="bg-white rounded-lg shadow-lg p-6 w-full max-w-4xl animate__animated animate__zoomIn animate__delay-1s lg:col-span-1">
        <div class="flex justify-between items-center mb-3 lg:mb-5">
          <h2 class="text-md text-blue-500 font-bold lg:text-xl">Users by messages</h2>
          <button class="text-sm bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 hover:cursor-pointer lg:text-base" @click="gotoLink('/api/users?query=most_messages')">Endpoint</button>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center"># Messages</th>
              <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase text-center">ID</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in users" :key="user.id">
              <td class="px-2 py-4 whitespace-nowrap text-sm font-medium text-gray-900 lg:px-6">
                {{ user.name }}
              </td>
              <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500 lg:px-6 text-center">
                {{ user.messages_count }}
              </td>
              <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500 lg:px-6 text-center">
                {{ user.id }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="bg-white col-span-1 rounded-lg shadow-lg p-6 w-full animate__animated animate__zoomIn animate__delay-1s lg:col-span-2">
        <div class="flex justify-between items-center mb-3 lg:mb-5">
          <h2 class="text-md text-blue-500 font-bold lg:text-xl">Conversations by highest rating</h2>
          <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 hover:cursor-pointer" @click="gotoLink('/api/conversations?query=highest_rated')">Endpoint</button>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Rating</th>
                <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center"># Messages</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="conversation in conversations" :key="conversation.id">
                <td class="px-6 py-4 text-sm font-medium text-gray-900 overflow-hidden overflow-ellipsis">
                  {{ conversation.conversation_id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                  {{ conversation.rating }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                  {{ conversation.messages.length }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="flex-col relative w-full h-96 bg-white rounded-lg shadow-lg py-10 p-6 flex justify-center items-center animate__animated animate__zoomIn animate__delay-1s lg:col-span-3 overflow-scroll">
        <h2 class="text-xl text-blue-500 font-bold mb-4">Interactions over time</h2>
        <canvas id="scatterChart" ref="scatterChart"></canvas>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from "chart.js/auto";
import "chartjs-adapter-moment";

export default {
  name: "StatsPage",
  data() {
    return {
      users: [],
      conversations: [],
      messages: [],
      loading: true,
      scatterChart: null,
    };
  },
  async created() {
    await this.getUsers();
    await this.getConversations();
    await this.getMessages();
    this.loading = false;
    this.createScatterChart();
  },
  components: {
    Chart,
  },
  methods: {
    async getUsers() {
      try {
        const response = await axios.get("/api/users", {
          params: {
            query: "most_messages",
          },
        });
        this.users = response.data;
      } catch (error) {
        console.error(error);
      }
    },
    async getConversations() {
      try {
        const response = await axios.get("/api/conversations", {
          params: {
            query: "highest_rated",
          },
        });
        this.conversations = response.data;
      } catch (error) {
        console.error(error);
      }
    },
    async getMessages() {
      try {
        const response = await axios.get("/api/messages");
        this.messages = response.data;
      } catch (error) {
        console.error(error);
      }
    },
    gotoLink(url) {
      window.open(url, "_blank");
    },
    createScatterChart() {
      const ctx = this.$refs.scatterChart.getContext("2d");

      // Prepare the data for the chart
      const chartData = this.conversations.flatMap((conversation) =>
        conversation.messages.map((message) => ({
          x: new Date(message.created_at),
          y: conversation.rating,
        })),
      );

      new Chart(ctx, {
        type: "scatter",
        data: {
          datasets: [
            {
              label: "Messages",
              data: chartData,
              backgroundColor: "rgba(75, 192, 192, 0.6)",
              borderColor: "rgba(75, 192, 192, 1)",
              borderWidth: 1,
              pointRadius: 5,
              pointHoverRadius: 7,
              hitRadius: 10,
            },
          ],
        },
        options: {
          clip: false,
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              type: "time",
              time: {
                unit: "hour",
                displayFormats: {
                  day: "MMM D, YYYY",
                },
              },
              title: {
                display: true,
                text: "Time Created",
              },
            },
            y: {
              beginAtZero: true,
              max: 5,
              title: {
                display: true,
                text: "Conversation Rating",
              },
            },
          },
          plugins: {
            tooltip: {
              callbacks: {
                title: function (context) {
                  return new Date(context[0].parsed.x).toLocaleString();
                },
                label: function (context) {
                  return `Rating: ${context.parsed.y}`;
                },
              },
            },
          },
        },
      });
    },
  },
};
</script>
