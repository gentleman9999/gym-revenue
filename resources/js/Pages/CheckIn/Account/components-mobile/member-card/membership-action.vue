<template>
    <div class="membership-action-container">
        <Button warning class="membership-action">Premium</Button>
        <Button accent class="membership-action">PT</Button>
    </div>
</template>
<style scoped>
.membership-action-container {
    @apply flex flex-row space-x-4 mt-6 mb-10;
}
.membership-action {
    @apply text-base-content font-bold;
}
</style>
<script setup>
import Button from "@/Components/Button.vue";
</script>
