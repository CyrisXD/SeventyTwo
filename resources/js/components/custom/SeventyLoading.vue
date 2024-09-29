<template>
  <div class="logo-container">
    <!-- Logo element that will be revealed -->
    <div class="logo"></div>
    <!-- Shimmer effect -->
    <div class="shimmer"></div>
  </div>
</template>

<script>
export default {
  name: "SeventyLoading",
};
</script>

<style scoped>
.logo-container {
  position: relative;
  width: 300px;
  height: 59px;
  overflow: hidden;
}

.logo {
  width: 100%;
  height: 100%;
  background-image: url("../../../assets/logo2.png");
  background-size: cover;
  background-position: center;
  opacity: 0; /* Initially hidden */
  animation: logo-reveal 1s linear forwards;
}

/* Shimmer effect styling */
.shimmer {
  position: absolute;
  top: 0;
  left: -100%; /* Start shimmer outside the container */
  width: 200%;
  height: 100%;
  background: linear-gradient(120deg, transparent 30%, rgba(255, 255, 255, 0.7) 50%, transparent 70%);
  transform: skewX(-20deg);
  animation: shimmer-effect 2s infinite;
}

/* Keyframes for the logo reveal */
@keyframes logo-reveal {
  0% {
    opacity: 1;
    clip-path: polygon(0 0, 0 0, 0 100%, 0 100%);
  }
  100% {
    opacity: 1;
    clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
  }
}

/* Shimmer animation */
@keyframes shimmer-effect {
  0% {
    left: -100%;
  }
  50% {
    left: 100%;
  }
  100% {
    left: -100%;
  }
}
</style>
