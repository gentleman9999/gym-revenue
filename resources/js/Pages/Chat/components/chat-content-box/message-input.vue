<template>
    <div
        v-if="isEditing"
        class="-mb-2 bg-gray-600 px-3 py-1 text-sm"
        role="button"
        @click="$emit('on-cancel-edit')"
    >
        <font-awesome-icon icon="times-circle" /> Cancel Editing
    </div>
    <div class="message-input-container mt-2">
        <textarea
            v-model="msg"
            ref="textArea"
            class="message-input text-sm"
            rows="1"
            @keypress="handleKeyPress"
        />
        <Button secondary size="sm" :disabled="disableSubmit" @click="sendMsg"
            >Send</Button
        >
    </div>
</template>
<style scoped>
.message-input-container {
    @apply border-t border-neutral-content/20 flex flex-row w-full items-center p-2 space-x-4;
}
.message-input {
    @apply bg-transparent rounded border border-secondary w-11/12;
}
</style>
<script setup>
import { ref, defineEmits, inject, computed, onMounted } from "vue";
import Button from "@/Components/Button.vue";
import MessageInfo from "@/Pages/Chat/models/MessageInfo.js";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faTimesCircle } from "@fortawesome/pro-solid-svg-icons";
library.add(faTimesCircle);

const emits = defineEmits(["on-cancel-edit"]);
const props = defineProps({
    message: {
        required: false,
        type: MessageInfo,
    },
});
const textArea = ref(null);
const msg = ref(props.message?.body ?? null);
const sendMessage = inject("sendMessage");
const disableSubmit = computed(() => {
    return (
        msg.value === null ||
        msg.value.trim() === "" ||
        (props.message && msg.value === props.message.body)
    );
});
const isEditing = computed(
    () => props.message && props.message.body.trim() !== ""
);

onMounted(() => {
    if (isEditing.value) {
        textArea.value.focus();
    }
});

/**
 * @param {KeyboardEvent} event
 */
function handleKeyPress(event) {
    if (event.key === "Enter" && !event.shiftKey) {
        event.preventDefault();
        sendMsg();
    }
}
function sendMsg() {
    sendMessage(msg.value, props.message);
    if (isEditing.value) {
        props.message.message.message = msg.value;
    }
    msg.value = "";
    emits("on-cancel-edit");
}
</script>
