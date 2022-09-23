<template xmlns="http://www.w3.org/1999/html">
    <div class="chat-messenger-list-container">
        <div>
            <div v-if="conversations.length === 0" class="text-center mt-10">
                No active chats
            </div>
            <chat-messenger
                v-else
                v-for="(conversation, index) in conversations"
                :key="index"
                :conversation="conversation"
                @click="$emit('view-conversation', index)"
            />
        </div>
    </div>
</template>
<style scoped>
.chat-messenger-list-container {
    @apply flex flex-col w-[380px] h-full overflow-x-hidden overflow-y-auto border-r border-neutral-content/70;
}
</style>
<script setup>
import ChatMessenger from "./chat-messenger/index.vue";
defineEmits(["view-conversation"]);
const props = defineProps({
    /** @type {Array<ConversationMsg>} */
    conversations: {
        type: Array,
        required: true,
    },
});
</script>
