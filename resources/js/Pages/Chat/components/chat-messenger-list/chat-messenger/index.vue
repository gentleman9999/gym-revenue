<template>
    <div
        class="px-3 py-2 border-b border-base-content/20 cursor-pointer conversation"
        :class="{
            'has-unread': hasUnreadMessage,
            'internal-chat': conversation.isInternal,
        }"
    >
        <div class="grid grid-cols-4">
            <div
                class="col-span-3 overflow-hidden whitespace-nowrap overflow-ellipsis name text-white"
            >
                {{ conversation.name }}
            </div>
            <div class="text-right">
                <span
                    v-show="hasUnreadMessage"
                    class="messenger-unread text-white"
                >
                    {{ conversation.unreadMessagesCount }}
                </span>
                <span class="text-xs">{{
                    conversation.lastMessage.dateFormatted()
                }}</span>
            </div>
        </div>
        <div
            class="overflow-hidden whitespace-nowrap overflow-ellipsis w-[370px] text-sm"
        >
            {{
                conversation.lastMessage.isInfo
                    ? "..."
                    : conversation.lastMessage.body
            }}
        </div>
    </div>
</template>
<style scoped>
.messenger-unread {
    @apply bg-secondary rounded-full px-1 text-[10px] mr-3;
}

.conversation {
    @apply text-gray-300;
}
.has-unread {
    @apply text-blue-400;
}
.internal-chat {
    border-left: 2px solid #ff0000;
}
</style>
<script setup>
import ConversationMsg from "@/Pages/Chat/models/ConversationMsg.js";
import { computed } from "vue";
const props = defineProps({
    conversation: ConversationMsg,
});

console.log(props.conversation);

const hasUnreadMessage = computed(
    () => props.conversation.unreadMessagesCount > 0
);
</script>
