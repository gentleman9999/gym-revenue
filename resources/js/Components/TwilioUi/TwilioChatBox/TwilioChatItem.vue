<template>
    <div class="chat-item">
        <span
            class="chat-name"
            :class="{
                'self-start': chat.user_id !== current_user_id,
                'self-end': chat.user_id === current_user_id,
            }"
            >{{ chat.user_name }}</span
        >
        <span
            class="chat-msg"
            :class="{
                'self-start': chat.user_id !== current_user_id,
                'self-end': chat.user_id === current_user_id,
            }"
            >{{ chat.msg }}</span
        >
    </div>
</template>
<style scoped>
.chat-item {
    @apply flex flex-col;
}
.chat-name {
    @apply text-sm;
}
.chat-msg {
    @apply bg-secondary rounded-md py-1 px-2;
}
</style>
<script setup>
import { usePage } from "@inertiajs/inertia-vue3";

const props = defineProps({
    chat: Object,
});

const current_user_id = usePage().props?.value?.user.id;
</script>
