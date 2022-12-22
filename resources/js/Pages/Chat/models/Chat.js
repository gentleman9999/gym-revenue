import Participant from "@/Pages/Chat/models/Participant.js";
import Message from "@/Pages/Chat/models/Message.js";
import MessageInfo from "@/Pages/Chat/models/MessageInfo.js";

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

        /** @type {Number} */
        this.adminId = chat.admin_id;

        /** @type {boolean} */
        this.isAdmin = chat.admin_id === userId;

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

    sendMessage(text, message) {
        if (message instanceof MessageInfo) {
            return message.updateAttributes({ message: text });
        }

        axios.post(route("message-internal-chats"), {
            message: text,
            chat_id: this.id,
        });
    }
}
