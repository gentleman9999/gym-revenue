export default class ConversationMsg {
    /**
     * @param {Conversation} conversation
     * @param {string} name
     * @param {string} sid the chat participant id of current user.
     * @param {MessageInfo[]} messages
     * @param {boolean} hasMultiParticipants
     * @param {Date} updatedAt
     * @param {boolean} isInternal
     */
    constructor(
        conversation,
        name,
        sid,
        messages,
        hasMultiParticipants,
        updatedAt,
        isInternal = false
    ) {
        this.conversation = conversation;
        this.name = name;
        this.sid = sid;
        this.messages = messages;
        this.hasMultiParticipants = hasMultiParticipants;
        this.updatedAt = updatedAt;
        this.isInternal = isInternal;
    }

    /**
     * @returns {MessageInfo}
     */
    get lastMessage() {
        return this.messages.at(-1);
    }

    get unreadMessagesCount() {
        return this.messages.filter(
            (msg) => !msg.attributes.read && !msg.isInfo
        ).length;
    }

    touch() {
        this.updatedAt = new Date();
    }
}
