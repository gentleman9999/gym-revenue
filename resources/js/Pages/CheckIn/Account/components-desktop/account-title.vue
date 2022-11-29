<template>
    <div class="account-title-container">
        <div>Account Page</div>
        <div class="account-title-filter">
            <Button
                outline
                size="xs"
                secondary
                @click="$emit('show-modal', 'guest-pass')"
            >
                Add Guest Past
            </Button>
            <Button
                outline
                size="xs"
                secondary
                @click="$emit('show-modal', 'mass-com')"
            >
                + Communication
            </Button>
            <Button
                size="xs"
                secondary
                @click="$inertia.visit(route('checkin.club'))"
            >
                Check-In
            </Button>
        </div>
    </div>
</template>
<style scoped>
.account-title-container {
    @apply flex flex-row w-full justify-between px-16;
    margin-top: -15px;
}
.account-title-filter .btn-secondary {
    @apply text-base-content border-secondary rounded;
}
.account-title-filter {
    @apply flex flex-row space-x-3 items-center flex-wrap lg:flex-nowrap;
    max-width: calc(100vw - 32px);
}
</style>
<script setup>
import { ref } from "vue";
import Button from "@/Components/Button.vue";
</script>
