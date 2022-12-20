<template>
    <div
        ref="messageElement"
        class="chat-message-container mb-1 text-sm relative"
        :class="{ mine: isMine, 'is-info': message.isInfo }"
        :title="message.dateFormatted()"
    >
        <div class="chat-message whitespace-pre-wrap">
            {{ message.body }}

            <Dropdown
                v-if="isMine"
                :align="isMine ? 'end' : 'start'"
                width="60"
                :class="['absolute', { 'right-1': isMine, 'left-1': !isMine }]"
            >
                <template #trigger>
                    <slot name="trigger">
                        <span class="hidden chat-menu-trigger" role="button">
                            <font-awesome-icon icon="ellipsis-v" size="sm" />
                        </span>
                    </slot>
                </template>
                <template #content>
                    <ul class="menu w-[150px]">
                        <li>
                            <a href="#" @click.prevent="edit">Edit</a>
                        </li>
                        <li>
                            <a href="#" @click.prevent="remove">Delete</a>
                        </li>
                    </ul>
                </template>
            </Dropdown>
        </div>
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
import { computed, onMounted, ref } from "vue";
import Dropdown from "@/Components/Dropdown.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faEllipsisV } from "@fortawesome/pro-solid-svg-icons";
library.add(faEllipsisV);

const emits = defineEmits(["on-edit", "on-remove"]);

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

function remove() {
    emits("on-remove", props.message);
}

function edit() {
    emits("on-edit", props.message);
}
</script>
<style scoped>
.chat-message-container {
    @apply flex flex-col w-fit space-y-1;
}
.chat-message {
    @apply rounded px-4 py-1 bg-secondary self-start;
}
.chat-message:hover .chat-menu-trigger {
    display: inline !important;
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
