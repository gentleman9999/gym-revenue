<template>
    <div class="chat-content-container">
        <div v-if="!conversation" class="text-center mt-10 text-2xl">
            No chat selected
        </div>
        <template v-else>
            <div class="chat-content-header">
                <span>{{ conversation.name }}</span>
            </div>
            <div
                class="flex flex-row h-full p-4 overflow-x-hidden overflow-y-auto"
            >
                <div class="chat-area">
                    <div ref="chatWrapper" class="chat-msg-container">
                        <chat-message
                            v-for="(message, i) in conversation.messages"
                            :key="i"
                            :message="message"
                            :has-multi-participants="
                                conversation.hasMultiParticipants
                            "
                            :is-mine="message.sid === conversation.sid"
                        />
                    </div>
                </div>
            </div>
            <message-input />
        </template>
    </div>
</template>
<style scoped>
.chat-content-container {
    @apply flex flex-col h-full overflow-hidden;
    width: calc(100% - 380px);
}
.chat-msg-container {
    @apply flex flex-col space-y-2;
}
.chat-area {
    @apply flex flex-col w-full h-full justify-between;
}
.chat-content-header {
    @apply flex flex-col border-b w-full pt-3 font-extrabold border-neutral-content/20 text-center h-14;
}
</style>
<script setup>
import ChatMessage from "./chat-message.vue";
import MessageInput from "./message-input.vue";
import ConversationMsg from "@/Pages/Chat/models/ConversationMsg.js";
import { onUpdated, ref } from "vue";
const props = defineProps({
    conversation: ConversationMsg,
});

/** @type {Ref<HTMLElement>} */
const chatWrapper = ref(null);

onUpdated(() => {
    chatWrapper.value.lastElementChild.scrollIntoView({
        block: "end",
        behavior: "smooth",
    });
});
</script>
