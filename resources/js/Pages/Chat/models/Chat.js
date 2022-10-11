import Participant from "@/Pages/Chat/models/Participant.js";
import Message from "@/Pages/Chat/models/Message.js";

export default class Chat {
    /** @type {Message[]} */
    messages = [];
    /** @type {Participant[]} */
    participants = [];
    /** @type {string|null} */
    currentUserParticipantId = null;

    constructor(chat, userId) {
        /** @type {string} */
        this.id = chat.id;

        /** @type {int} */
        this.createdBy = chat.created_by;

        /** @type {Date} */
        this.updatedAt = chat.updated_at ? new Date(chat.updated_at) : null;

        /** @type {Date} */
        this.createdAt = chat.created_at
            ? new Date(chat.created_at)
            : new Date();

        if (this.participants) {
            this.participants = chat.participants.map(
                (p) => new Participant(p)
            );

            this.currentUserParticipantId = chat.participants.find(
                ({ user }) => user.id === userId
            )?.id;
        }

        if (chat.messages) {
            this.messages = chat.messages.map(
                (m) =>
                    new Message(
                        m,
                        this.participants,
                        this.currentUserParticipantId
                    )
            );
        }
    }

    sendMessage(message, attribute) {
        axios.post(route("message-internal-chats"), {
            message,
            chat_id: this.id,
        });
    }
}
