<template>
    <div class="chat-container">
        <chat-messenger-list
            :conversations="conversations"
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
import { provide, ref } from "vue";
import ChatContentBox from "./components/chat-content-box/index.vue";
import ChatMessengerList from "./components/chat-messenger-list/index.vue";
import { Client as ConversationsClient } from "@twilio/conversations";
import ConversationMsg from "@/Pages/Chat/models/ConversationMsg.js";
import MessageInfo from "@/Pages/Chat/models/MessageInfo.js";

const conversations = ref({});
const activeConversation = ref(null);

axios.get(route("twilio.api-token")).then(({ data }) => {
    const client = new ConversationsClient(data.token);

    client.on("messageAdded", async (message) => {
        conversations.value[message.conversation.sid].messages.push(
            MessageInfo.buildFromMessage(message)
        );
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
                response.data.map(
                    ({ conversation_id, user_conversation_id }) => {
                        addConversation(conversation_id, user_conversation_id);
                    }
                );
            });
        }
    });

    async function addConversation(conversationSid, mySid) {
        // https://www.twilio.com/docs/conversations/working-with-conversations?code-sample=code-list-conversations&code-language=JavaScript&code-sdk-version=default
        const conversation = await client.getConversationBySid(conversationSid);
        const participants = await conversation.getParticipants();

        // https://media.twiliocdn.com/sdk/js/conversations/releases/2.0.0/docs/classes/Conversation.html#getMessages
        conversation.getMessages().then(({ items }) => {
            conversations.value[conversation.sid] = new ConversationMsg(
                conversation,
                participants
                    .filter((p) => p.sid !== mySid)
                    .map((p) => p.identity || p.attributes.identity)
                    .join(", "),
                mySid,
                items.map(MessageInfo.buildFromMessage)
            );
        });
    }
});

provide("sendMessage", sendMessage);

function setActiveConversation(conversationSid) {
    activeConversation.value = conversations.value[conversationSid];
}

function sendMessage(message) {
    activeConversation.value.conversation.sendMessage(message);
}
</script>
