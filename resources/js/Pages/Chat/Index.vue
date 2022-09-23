<template>
    <div class="chat-container">
        <chat-messenger-list
            :key="refreshKey"
            :conversations="sortedConversation"
            @view-conversation="setActiveConversation"
        />
        <chat-content-box
            :conversation="activeConversation"
            @sendMsg="sendMessage"
        />
    </div>
</template>
<style scoped>
.chat-container {
    @apply flex flex-row;
    margin-top: -32px;
    height: calc(100vh - 140px);
}
</style>
<script setup>
import { computed, provide, ref } from "vue";
import ChatContentBox from "./components/chat-content-box/index.vue";
import ChatMessengerList from "./components/chat-messenger-list/index.vue";
import { Client as ConversationsClient } from "@twilio/conversations";
import ConversationMsg from "@/Pages/Chat/models/ConversationMsg.js";
import MessageInfo from "@/Pages/Chat/models/MessageInfo.js";

/** @type {Ref<Array<ConversationMsg>>} */
const conversations = ref([]);
const activeConversation = ref(null);
const refreshKey = ref(0);

const sortedConversation = computed(() => {
    // Show the latest conversation at the top of the list.
    return conversations.value.sort((a, b) => b.updatedAt - a.updatedAt);
});

axios.get(route("twilio.api-token")).then(({ data }) => {
    const client = new ConversationsClient(data.token);

    client.on("messageUpdated", ({ updateReasons }) => {
        if (updateReasons.includes("attributes")) {
            refreshKey.value++;
        }
    });

    client.on("messageAdded", async (message) => {
        const index = conversations.value.findIndex(
            (c) => c.conversation.sid === message.conversation.sid
        );
        /** @type {ConversationMsg} */
        const conversation = conversations.value[index];
        conversation.messages.push(new MessageInfo(message));
        conversation.touch();

        // Update last_active time for conversation
        axios.put(
            route("twilio.update-conversation", message.conversation.sid),
            {
                last_active: new Date(),
            }
        );
    });

    client.on("connectionStateChanged", async (state) => {
        if (state === "connected") {
            axios.get(route("twilio.get-conversation")).then((response) => {
                response.data.forEach(
                    ({ conversation_id, user_conversation_id, updated_at }) => {
                        addConversation(
                            conversation_id,
                            user_conversation_id,
                            new Date(updated_at)
                        );
                    }
                );
            });
        }
    });

    /**
     *
     * @param {string} conversationSid
     * @param {string} mySid
     * @param {Date} updatedAt
     * @returns {Promise<void>}
     */
    async function addConversation(conversationSid, mySid, updatedAt) {
        // https://www.twilio.com/docs/conversations/working-with-conversations?code-sample=code-list-conversations&code-language=JavaScript&code-sdk-version=default
        const conversation = await client.getConversationBySid(conversationSid);
        const participants = await conversation.getParticipants();

        // https://media.twiliocdn.com/sdk/js/conversations/releases/2.0.0/docs/classes/Conversation.html#getMessages
        conversation.getMessages().then(({ items }) => {
            conversations.value.push(
                new ConversationMsg(
                    conversation,
                    participants
                        .filter((p) => p.sid !== mySid)
                        .map((p) => p.identity || p.attributes.identity)
                        .join(", "),
                    mySid,
                    items.map((message) => new MessageInfo(message)),
                    participants.length > 2,
                    updatedAt
                )
            );
        });
    }
});

provide("sendMessage", sendMessage);

function setActiveConversation(index) {
    activeConversation.value = conversations.value[index];
}

function sendMessage(message) {
    activeConversation.value.conversation.sendMessage(message, { read: true });
}
</script>
