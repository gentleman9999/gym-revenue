import Participant from "@/Pages/Chat/models/Participant.js";

export default class Message {
    /**
     * @param {Object} message
     * @param {Participant[]} participants
     * @param {string} currentUserParticipantId
     */
    constructor(message, participants, currentUserParticipantId) {
        /** @type {string} */
        this.id = message.id;

        /** @type {string} */
        this.currentUserParticipantId = currentUserParticipantId;

        /** @type {string} */
        this.chatId = message.chat_id;

        /** @type {string} */
        this.chatParticipantId = message.chat_participant_id;

        /** @type {string} */
        this.message = message.message;

        /** @type {Date} */
        this.updatedAt = message.updated_at
            ? new Date(message.updated_at)
            : null;

        /** @type {Date} */
        this.createdAt = message.created_at ? new Date() : new Date();

        /** @type {string[]} the participant ids of users who have viewed this message */
        this.readBy = message.read_by ?? [];

        /** @type {Participant[]} */
        this.participants = participants ?? [];
    }

    get body() {
        return this.message;
    }

    get dateCreated() {
        return this.createdAt;
    }

    get author() {
        return this.participants.find((p) => p.id === this.chatParticipantId)
            .user.firstName;
    }

    get participantSid() {
        return this.chatParticipantId;
    }

    get attributes() {
        return {
            read: this.readBy.includes(this.currentUserParticipantId),
        };
    }

    updateAttributes(attribute) {
        if (!this.id) {
            return;
        }

        if (attribute.read === true) {
            this.readBy.push(this.currentUserParticipantId);
            attribute.read_by = [...new Set(this.readBy)];
        }

        axios.put(route("edit-internal-chat", this.id), attribute);
        console.log("attribute:", attribute);
    }

    remove() {
        axios.delete(route("delete-internal-chat", this.id));
    }
}
