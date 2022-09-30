<template>
    <div
        ref="messageElement"
        class="chat-message-container mb-1 text-sm"
        :class="{ mine: isMine, 'is-info': message.isInfo }"
        :title="message.dateFormatted()"
    >
        <div class="chat-message whitespace-pre-wrap">{{ message.body }}</div>
        <div
            v-if="hasMultiParticipants && !isMine"
            class="text-[9px] text-gray-300 author"
        >
            {{ message.author }}
        </div>
    </div>
</template>
<script setup>
import MessageInfo from "@/Pages/Chat/models/MessageInfo.js";
import { onMounted, ref } from "vue";

const props = defineProps({
    message: MessageInfo,
    isMine: Boolean,
    hasMultiParticipants: Boolean,
});

/** @type {Ref<HTMLElement>} */
const messageElement = ref(null);

onMounted(function () {
    setTimeout(() => props.message.markAsRead(), 1000);
});
</script>
<style scoped>
.chat-message-container {
    @apply flex flex-col w-fit space-y-1;
}
.chat-message {
    @apply rounded px-4 py-1 bg-secondary self-start;
}
.mine,
.mine .chat-message {
    @apply ml-auto;
}
.mine .chat-message {
    @apply bg-green-700;
}
.author {
    margin: 0 !important;
}
.is-info {
    @apply text-xs block text-center opacity-50 w-full;
}
.is-info .chat-message {
    @apply bg-transparent text-xs;
}
</style>
