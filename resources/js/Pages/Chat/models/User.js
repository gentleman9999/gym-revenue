export default class User {
    constructor(user) {
        /** @type {int} */
        this.id = user.id;

        /** @type {string} */
        this.firstName = user.first_name;

        /** @type {string} */
        this.lastName = user.last_name;

        /** @type {string} */
        this.email = user.email;

        /** @type {string} */
        this.emailVerifiedAt = user.email_verified_at;

        /** @type {string} */
        this.phone = user.phone;

        // add more if you need it.

        /** @type {Date} */
        this.updatedAt = new Date(user.updated_at);

        /** @type {Date} */
        this.createdAt = new Date(user.created_at);
    }

    get fullName() {
        return `${this.firstName} ${this.lastName}`;
    }
}
