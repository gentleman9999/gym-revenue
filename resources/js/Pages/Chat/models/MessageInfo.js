import * as InternalMessage from "@/Pages/Chat/models/Message.js";
import * as InternalParticipant from "@/Pages/Chat/models/Participant.js";

export default class MessageInfo {
    /**
     * @param {Message|InternalMessage} message
     * @param {boolean} isInfo
     * @param {InternalParticipant[]} participants
     */
    constructor(message, isInfo = false, participants = []) {
        this.date = new Date();
        this.message = message;
        this.isInfo = isInfo;
        this.participants = participants;
    }

    /**
     * @returns {string}
     */
    get body() {
        return this.message.body;
    }

    /**
     * @returns {Date}
     */
    get dateCreated() {
        return this.message.dateCreated;
    }

    /**
     * @returns {string}
     */
    get author() {
        if (this.isInfo) {
            return "";
        }

        return this.message.author;
    }

    /**
     * @returns {string}
     */
    get participantSid() {
        return this.message.participantSid;
    }

    /**
     * @returns {Object}
     */
    get attributes() {
        return this.message.attributes;
    }

    /**
     * @returns {void}
     */
    markAsRead() {
        if (!this.attributes.read) {
            this.message.updateAttributes({ read: true });
        }
    }

    dateFormatted() {
        const time = this.dateCreated
            .toLocaleTimeString()
            .replace(/(:\d{2})( [AP]M)$/, "$2");
        if (
            this.dateCreated.setHours(0, 0, 0, 0) ===
            this.date.setHours(0, 0, 0, 0)
        ) {
            return time;
        }

        return this.dateCreated.toLocaleDateString();
    }
}
