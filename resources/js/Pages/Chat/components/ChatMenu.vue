<template>
    <div class="chat-menu">
        <div class="bg-base-100 px-3 py-3 font-bold">
            <span @click="$emit('on-menu-close')" role="button">
                <font-awesome-icon icon="times-circle" />
            </span>
            <span class="ml-14">Conversation Info</span>
        </div>

        <div v-if="conversation.hasMultiParticipants" class="bg-base-100 mt-2">
            <div class="title pt-2 text-center">
                <span class="m-2 opacity-25">Participants</span>
                <Button size="xs" outline @click="openModal"> Add </Button>
            </div>

            <div class="divide-y divide-slate-700">
                <div
                    v-for="participant in participants"
                    :key="participant.user.id"
                    class="px-3 py-2"
                >
                    <span
                        :title="
                            conversation.conversation.adminId ===
                            participant.user.id
                                ? 'Owner'
                                : null
                        "
                    >
                        <font-awesome-icon icon="user-circle" />
                        <span
                            class="float-right opacity-25 text-xs hidden admin mt-1"
                            >ADMIN</span
                        >
                    </span>
                    <template v-if="participant.user.id === user.id">
                        <span class="ml-2">You</span>
                    </template>
                    <template v-else>
                        <span class="ml-2">{{
                            participant.user.fullName
                        }}</span>

                        <Dropdown
                            v-if="conversation.conversation.isAdmin"
                            align="end"
                            width="60"
                            class="float-right"
                            role="button"
                        >
                            <template #trigger>
                                <slot name="trigger">
                                    <font-awesome-icon
                                        icon="ellipsis-v"
                                        size="sm"
                                    />
                                </slot>
                            </template>
                            <template #content>
                                <ul class="menu w-[150px]">
                                    <li>
                                        <a
                                            href="#"
                                            @click.prevent="
                                                remove(participant.id)
                                            "
                                            >Remove</a
                                        >
                                    </li>
                                </ul>
                            </template>
                        </Dropdown>
                    </template>
                </div>
            </div>
        </div>

        <div class="divide-y divide-slate-700 bg-base-100 mt-2">
            <div
                class="px-3 py-2 text-red-600"
                role="button"
                @click="
                    remove(conversation.conversation.currentUserParticipantId)
                "
            >
                Leave Chat
            </div>
        </div>

        <DaisyModal ref="participantsModal">
            <GroupChatForm
                @start-chat="addParticipants"
                btn-label="Add"
                :ignore="participants.map((p) => p.user)"
            />
        </DaisyModal>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import User from "@/Pages/Chat/models/User.js";
import ConversationMsg from "@/Pages/Chat/models/ConversationMsg.js";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faEllipsisV,
    faTimesCircle,
    faUserCircle,
} from "@fortawesome/pro-solid-svg-icons";
import Dropdown from "@/Components/Dropdown.vue";
import GroupChatForm from "@/Pages/Chat/components/GroupChatForm.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import Button from "@/Components/Button.vue";

library.add(faTimesCircle, faUserCircle, faEllipsisV);

const props = defineProps({
    conversation: ConversationMsg,
    user: User,
});

const participants = computed(
    () => props.conversation.conversation.participants
);
const participantsModal = ref(null);

function remove(participantId) {
    axios.delete(route("delete-chat-participant", participantId));
}

function openModal() {
    participantsModal.value.open();
}

function addParticipants(participants) {
    participantsModal.value.close();
    axios.post(
        route("add-chat-participant", props.conversation.conversation.id),
        {
            participant_ids: Object.values(participants).map((p) => p.id),
        }
    );
}
</script>

<style scoped>
.chat-menu {
    @apply flex flex-col w-[380px] bg-base-300 h-full overflow-x-hidden overflow-y-auto border-r border-neutral-content/30;
}

span[title="Owner"] {
    @apply text-yellow-600;
}

span[title="Owner"] .admin {
    display: inline;
}
</style>
