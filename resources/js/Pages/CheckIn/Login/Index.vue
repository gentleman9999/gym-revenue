<template>
    <lock-screen v-if="mode === 'lock'" @toggle-mode="mode = 'login'" />
    <login-screen v-else />
</template>
<style scoped></style>
<script setup>
import { ref } from "vue";
import LockScreen from "./lock-screen.vue";
import LoginScreen from "./login-screen.vue";
const mode = ref("lock");
</script>
