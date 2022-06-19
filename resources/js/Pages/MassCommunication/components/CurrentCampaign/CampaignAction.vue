<template>
    <div
        class="campaign-action"
        :class="{
            'border-secondary': action !== 'sms',
            'border-base-content': action === 'sms',
        }"
    >
        <sms-icon v-if="action === 'sms'" />
        <chat-icon v-if="action === 'chat'" />
        <phone-icon v-if="action === 'phone'" />
        <to-do24-icon v-if="action === 'todo'" />
    </div>
</template>
<style scoped>
.campaign-action {
    @apply flex items-center justify-center border rounded bg-neutral-900 text-base-content w-12 h-11 m-auto;
}
</style>
<script setup>
import { SmsIcon, ChatIcon, PhoneIcon, ToDo24Icon } from "@/Components/Icons";
const props = defineProps({
    action: {
        type: String,
        default: "sms",
    },
});
</script>
