<template>
    <div class="px-3 py-2 border-b border-base-content/20 cursor-pointer">
        <div class="grid grid-cols-4">
            <div
                class="col-span-3 overflow-hidden whitespace-nowrap overflow-ellipsis"
            >
                {{ conversation.name }}
            </div>
            <div class="text-right">
                <span class="messenger-unread">2</span>
                <span class="text-xs text-gray-300">{{
                    conversation.lastMessage.dateFormatted()
                }}</span>
            </div>
        </div>
        <div
            class="overflow-hidden whitespace-nowrap overflow-ellipsis w-[370px] text-sm text-gray-300"
        >
            {{ conversation.lastMessage.body }}
        </div>
    </div>
</template>
<style scoped>
.messenger-unread {
    @apply bg-secondary rounded-full px-1 text-[10px] mr-3;
}
</style>
<script setup>
import ConversationMsg from "@/Pages/Chat/models/ConversationMsg.js";
const props = defineProps({
    conversation: ConversationMsg,
});
</script>
