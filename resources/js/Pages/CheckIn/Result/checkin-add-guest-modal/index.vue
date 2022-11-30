<template>
    <div class="add-guest-container">
        <div class="add-guest-title">Kevin Buchanan’s Guest</div>
        <input
            class="add-guest-input bg-success/60"
            placeholder="Name"
            v-model="guestName"
        />
        <div class="mb-4">And enter...</div>
        <add-method />
        <Button secondary>Check-In</Button>
    </div>
</template>
<style scoped>
.add-guest-container {
    @apply flex flex-col items-center text-center mb-2;
}
.add-guest-title {
    @apply text-xl font-bold mt-2;
}

.add-guest-input {
    @apply my-6;
}
.add-guest-input {
    @apply text-lg text-center text-base-content rounded h-10 pl-1 focus:outline-none;
    &:focus:focus {
        --tw-ring-shadow: transparent;
    }
}
</style>
<script setup>
import { ref } from "vue";
import { SmsIcon, PhoneIcon } from "@/Components/Icons";
import AddMethod from "./add-method.vue";
import Button from "@/Components/Button.vue";
const guestName = ref("");
</script>
