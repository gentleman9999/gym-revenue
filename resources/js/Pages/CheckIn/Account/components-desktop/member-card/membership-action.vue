<template>
    <div class="membership-action-container">
        <div class="flex flex-row space-x-3">
            <Button warning size="sm" class="membership-action">Premium</Button>
            <Button accent size="sm" class="membership-action">PT</Button>
        </div>
        <div class="flex flex-row space-x-3">
            <Button outline size="sm" class="membership-action">Phone</Button>
            <Button outline size="sm" class="membership-action">E-mail</Button>
            <Button outline size="sm" class="membership-action">SMS</Button>
        </div>
    </div>
</template>
<style scoped>
.membership-action-container {
    @apply flex flex-row space-x-4 bg-secondary px-7 py-4 justify-between;
    margin-left: -1rem;
    margin-right: -1rem;
}
.membership-action {
    @apply text-base-content font-bold text-lg;
}
</style>
<script setup>
import Button from "@/Components/Button.vue";
</script>
