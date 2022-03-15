<template>
    <div v-if="loading" class="text-center">
        <label>Seeing Who You Can Impersonate...</label>
    </div>
    <div v-else>
        <p>Scroll through this list to select a user you want to impersonate.</p>
        <div v-if="users.length > 0" class="pt-4 max-h-60 overflow-y-scroll">
            <table class="table w-full table-compact" :class="{ 'table-zebra': true }">
                <tbody>
                    <tr v-for="(user, idx) in users">
                        <td>{{ user.name }}</td>
                        <td>{{ user.role }}</td>
                        <td> <button type="button" class="btn btn-info" @click="impersonateUser(user.userId)"> Impersonate </button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="text-center">
            <p>No Users Available that you can Impersonate.</p>
        </div>
    </div>
</template>

<script>
import { Inertia } from "@inertiajs/inertia";

export default {
    name: "ListOfUsersToImpersonate",
    components: {},
    props: [],
    watch: {},
    data() {
        return {
            loading: false,
            users: []
        }
    },
    computed: {},
    methods: {
        getList() {
            let _this = this;
            this.loading = true;

            axios.post('/impersonation/users', {})
                .then(({ data }) => {
                    _this.loading = false;
                    _this.users = data;
                })
                .catch(err => {
                    _this.loading = false;
                    _this.users = [];
                })
        },
        impersonateUser(userId) {
            Inertia.post(route("impersonation.start", { victimId: userId}));
            this.$emit('close');
        }
    },
    mounted() {
        this.getList();
    }
}
</script>

<style scoped>

</style>
