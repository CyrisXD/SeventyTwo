<template>
  <nav class="bg-white drop-shadow-2xl py-5 align-middle justify-center flex gap-4 h-1/6">
    <router-link to="/" class="text-lg font-bold hover:text-blue-500" active-class="text-blue-500">Home</router-link>
    <router-link to="/stats" class="text-lg font-bold hover:text-blue-500" active-class="text-blue-500">Statistics</router-link>
    <!-- External links -->
    <a href="https://github.com/CyrisXD/SeventyTwo" target="_blank" class="text-lg font-bold hover:text-blue-500" active-class="text-blue-500">Repository</a>
  </nav>
  <main class="flex flex-row flex-1 justify-center w-screen">
    <router-view></router-view>
  </main>
</template>
