<template>
    <div
        class="twilo-chat-box"
        :class="{
            'h-0 opacity-0': !showChat,
            'h-96 opacity-100': showChat,
        }"
    >
        <div
            class="twilo-chat-items"
            :class="{
                'h-0 opacity-0': !showChat,
                'h-80 opacity-100': showChat,
            }"
        >
            <Button class="close-btn" @click="$emit('toggle')">X</Button>
            <div class="flex flex-col flex-wrap space-y-2">
                <twilo-chat-item
                    v-for="chat in mockChat"
                    :key="chat.id"
                    :chat="chat"
                />
            </div>
        </div>
        <twilo-chat-input @send-msg="sendMsg" />
    </div>
</template>
<style scoped>
.twilo-chat-box {
    @apply bg-base-100 w-64 rounded border border-secondary flex flex-col justify-between;
    transition: height 650ms cubic-bezier(0.18, 0.89, 0.32, 1.28);
}
.twilo-chat-items {
    @apply overflow-y-auto p-2;
}
.close-btn {
    @apply h-12 w-12 absolute rounded-full;
    top: -1rem;
    right: -1rem;
}
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
import Button from "@/Components/Button.vue";
import { usePage } from "@inertiajs/inertia-vue3";
import TwiloChatItem from "./TwiloChatItem.vue";
import TwiloChatInput from "./TwiloChatInput.vue";
const props = defineProps({
    showChat: Boolean,
});

const mockChat = [
    {
        id: 1,
        user_id: 1,
        user_name: "Fred",
        msg: "Fred's message",
    },
    {
        id: 2,
        user_id: 2,
        user_name: "John",
        msg: "John's message",
    },
    {
        id: 3,
        user_id: 24,
        user_name: "James",
        msg: "James's message",
    },
];

const sendMsg = (msg) => {
    console.log(msg);
};
</script>
