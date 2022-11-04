<template>
    <div class="invite-form-container">
        <div class="mb-5 font-semibold">Invite Friends</div>
        <div v-for="item in data" :key="item.id" class="invite-form-item">
            <div class="invite-form-label">{{ item.label }}</div>
            <input v-model="form[item.id]" class="invite-form-input" />
        </div>
        <Button secondary class="mx-auto mt-8">Send Invite</Button>
    </div>
</template>
<style scoped>
.invite-form-container {
    @apply flex flex-col items-center text-lg;
}
.invite-form-item {
    @apply flex flex-col space-y-2.5 mb-2.5;
    width: 220px;
}
.invite-form-label {
    @apply text-lg text-secondary;
}
.invite-form-input {
    @apply text-lg rounded h-10 pl-1 focus:outline-none bg-base-content/20;
    &:focus:focus {
        --tw-ring-shadow: transparent;
    }
}
</style>
<script setup>
import Button from "@/Components/Button.vue";
import { useGymRevForm } from "@/utils";
const form = useGymRevForm({});
const data = [
    {
        id: "friend_name",
        label: "Friends Name",
        required: true,
    },
    {
        id: "friend_email",
        label: "Friends E-mail",
    },
    {
        id: "friend_phone",
        label: "Friends Phone Number",
    },
];
</script>
