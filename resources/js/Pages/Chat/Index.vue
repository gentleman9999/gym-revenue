<template>
    <div v-show="hasError" class="absolute w-full bg-red-600 text-center top-0">
        {{ error }}
    </div>
    <div
        ref="chatContainer"
        class="chat-container"
        :class="{ 'has-error': hasError }"
        data-chat-external-target-attr="chat-container"
    >
        <ChatMessengerList
            :key="refreshKey"
            :conversations="sortedConversation"
            @view-conversation="setActiveConversation"
            @start-chat="startChat"
        />

        <ChatContentBox
            :conversation="activeConversation"
            :is-chat-menu-open="isChatMenuOpen"
            @sendMsg="sendMessage"
            @on-menu-open="isChatMenuOpen = true"
        />

        <ChatMenu
            :key="isChatMenuOpen"
            v-if="activeConversation && isChatMenuOpen"
            :conversation="activeConversation"
            :user="user"
            @on-menu-close="isChatMenuOpen = false"
        />
    </div>
</template>
<style scoped>
.chat-container {
    @apply flex flex-row;
    margin-top: -35px;
    /* 64px header and 64px footer, please fix this if you know a better way */
    height: calc(100vh - (64px + 64px));
}
.chat-container.has-error {
    margin-top: -10px !important;
}
</style>
<script setup>
import { computed, onMounted, provide, ref } from "vue";
import ChatContentBox from "./components/chat-content-box/index.vue";
import ChatMessengerList from "./components/chat-messenger-list/index.vue";
import { Client as ConversationsClient } from "@twilio/conversations";
import ConversationMsg from "@/Pages/Chat/models/ConversationMsg.js";
import MessageInfo from "@/Pages/Chat/models/MessageInfo.js";
import Chat from "@/Pages/Chat/models/Chat.js";
import User from "@/Pages/Chat/models/User.js";
import { NOTIFICATION_TYPES } from "@/utils/index.js";
import Message from "@/Pages/Chat/models/Message.js";
import ChatMenu from "@/Pages/Chat/components/ChatMenu.vue";
import Participant from "@/Pages/Chat/models/Participant.js";
const props = defineProps({
    error: {
        type: String,
        required: false,
    },
    user: {
        type: User,
        required: true,
    },
});

/** @type {Ref<Array<ConversationMsg>>} */
const conversations = ref([]);
const activeConversation = ref(null);
/** @type {Ref<HTMLElement>} */
const chatContainer = ref(null);
const refreshKey = ref(0);
const isChatMenuOpen = ref(false);

const sortedConversation = computed(() => {
    // Show the latest conversation at the top of the list.
    return conversations.value.sort((a, b) => b.updatedAt - a.updatedAt);
});

const hasError = computed(() => props.error !== null);
let client = null;

onMounted(() => {
    // I'm attaching a refresh event to the chat element, so it can be refreshed out of this components scope.
    // @see resources/js/utils/parseNotificationResponse.buildConversationNotification
    // An alternative to this would be to use "usePage", but somehow it feels hackery.
    chatContainer.value.addEventListener("refresh", ({ detail }) => {
        if (detail.type === NOTIFICATION_TYPES.TYPE_NEW_CONVERSATION) {
            fetchTwilioConversation();
        } else {
            const index = conversations.value.findIndex(
                (c) => c.conversation.id === detail.chat_id
            );

            // new chat
            if (index < 0) {
                return fetchInternalConversation();
            }

            /** @type {ConversationMsg} */
            const conversation = conversations.value[index];
            if (detail.type === NOTIFICATION_TYPES.TYPE_NEW_CHAT_MESSAGE) {
                conversation.updatedAt = new Date();
                conversation.messages.push(
                    new MessageInfo(
                        new Message(detail.message, [], conversation.sid),
                        false,
                        conversation.conversation
                    )
                );
            } else if (
                detail.type === NOTIFICATION_TYPES.TYPE_UPDATED_CHAT_MESSAGE
            ) {
                const { message } = conversation.messages.find(
                    ({ message }) => message.id === detail.message.id
                );
                message.message = detail.message.message;
            } else if (
                detail.type === NOTIFICATION_TYPES.TYPE_DELETED_CHAT_MESSAGE
            ) {
                const index1 = conversation.messages.findIndex(
                    ({ message }) => message.id === detail.message.id
                );
                conversation.messages.splice(index1, 1);
            } else if (
                detail.type === NOTIFICATION_TYPES.TYPE_DELETED_CHAT_PARTICIPANT
            ) {
                const index1 = conversation.conversation.participants.findIndex(
                    ({ id }) => id === detail.participant.id
                );
                conversation.conversation.participants.splice(index1, 1);
            } else if (
                detail.type === NOTIFICATION_TYPES.TYPE_NEW_CHAT_PARTICIPANT
            ) {
                conversation.conversation.participants.push(
                    new Participant(detail.participant)
                );
            } else if (detail.type === NOTIFICATION_TYPES.TYPE_DELETED_CHAT) {
                conversations.value.splice(index, 1);
                activeConversation.value = null;
            }
        }
    });
});

fetchInternalConversation();

axios.get(route("twilio.api-token")).then(({ data }) => {
    client = new ConversationsClient(data.token);

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
            fetchTwilioConversation();
        }
    });
});

provide("sendMessage", sendMessage);

function setActiveConversation(index) {
    activeConversation.value = conversations.value[index];
    isChatMenuOpen.value = false;
}

function fetchTwilioConversation() {
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

/**
 * Remove any conversation where the $key property matches $match.
 * @param {string} key
 * @param {string} match
 */
function removeConversation(key, match) {
    // Currently, when we have a new conversation, we clear existing conversation and reset them.
    // The issue with this approach is that the rerender is very noticeable, so instead, we want
    // to take out existing conversation one at a time.
    const index = conversations.value.findIndex(
        (m) => m.conversation[key] === match
    );
    if (index >= 0) {
        conversations.value.splice(index, 1);
    }
}

function fetchInternalConversation() {
    axios.get(route("get-internal-chats")).then(({ data }) => {
        data.data.forEach(createConversationFromChatObject);
    });
}

function sendMessage(message, attribute) {
    activeConversation.value.conversation.sendMessage(message, attribute);
}

function startChat(users) {
    axios
        .post(route("start-internal-chats"), {
            participant_ids: Object.keys(users),
        })
        .then(({ data }) => {
            setActiveConversation(createConversationFromChatObject(data) - 1);
        });
}

function createConversationFromChatObject(data) {
    const chat = new Chat(data, props.user.id);
    const users = chat.participants
        .filter(({ user }) => user.id !== props.user.id)
        .map(({ user }) => user.fullName)
        .join(", ");
    let isInfo = false;

    removeConversation("id", chat.id);
    if (chat.messages.length === 0) {
        isInfo = true;
        chat.messages.push(
            new Message(
                {
                    chat_id: chat.id,
                    message: `Start conversation with ${users} by typing bellow`,
                },
                chat.participants,
                chat.currentUserParticipantId
            )
        );
    }

    return conversations.value.push(
        new ConversationMsg(
            chat,
            users,
            chat.currentUserParticipantId,
            chat.messages.map(
                (message) => new MessageInfo(message, isInfo, chat.participants)
            ),
            chat.participants.length > 2,
            chat.updatedAt,
            true
        )
    );
}

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
        const users = participants
            .filter((p) => p.sid !== mySid)
            .map((p) => p.identity || p.attributes.identity);
        const messages = items.map((message) => new MessageInfo(message));
        const firstMessage = messages.length === 0;
        if (firstMessage) {
            // This is probably a conversation we tried to start by clicking the send sms on lead/member contact page.
            messages.push(
                new MessageInfo(
                    {
                        body: `Start conversation with ${users.at(
                            0
                        )} by typing bellow`,
                        dateCreated: new Date(),
                        attributes: { read: true },
                    },
                    true
                )
            );
        }

        removeConversation("sid", conversation.sid);
        conversations.value.push(
            new ConversationMsg(
                conversation,
                users.join(", "),
                mySid,
                messages,
                participants.length > 2,
                updatedAt
            )
        );

        if (firstMessage) {
            // give some lee way for conversation to render.
            setTimeout(() => setActiveConversation(0), 300);
        }
    });
}
</script>
