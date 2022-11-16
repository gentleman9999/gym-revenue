<template>
    <account-mobile v-if="isMobile" />
    <account-desktop v-if="!isMobile" />
</template>
<style scoped></style>
<script setup>
import { computed } from "vue";
import AccountMobile from "./account-mobile.vue";
import AccountDesktop from "./account-desktop.vue";
const isMobile = computed(() => window.innerWidth <= 480);
</script>
