import User from "@/Pages/Chat/models/User.js";

export default class Participant {
    constructor(participant) {
        /** @type {string} */
        this.id = participant.id;

        /** @type {string} */
        this.chatId = participant.chat_id;

        /** @type {int} */
        this.userId = participant.user_id;

        /** @type {Date} */
        this.updatedAt = new Date(participant.updated_at);

        /** @type {Date} */
        this.createdAt = new Date(participant.created_at);

        /** @type {User} */
        this.user = new User(participant.user);
    }
}
