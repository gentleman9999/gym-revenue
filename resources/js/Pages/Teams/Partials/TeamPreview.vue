<template>
    <div className="grid grid-cols-6 gap-6">
        <div className="field col-span-3 lg:col-span-3">
            <label>Team Name:</label>
            <div class="data">
                {{ team.team.name }}
            </div>
        </div>

        <div className="field col-span-3 lg:col-span-3" v-if="team.client">
            <label>Client Name:</label>
            <div class="data" v-if="team.client">
                {{ team.client.name }}
            </div>
        </div>
    </div>

    <div className="grid grid-cols-6 gap-6 mt-4">
        <div className="form-control col-span-6 lg:col-span-4">
            <h1>Users</h1>
            <table class="table table-compact w-full" v-if="team.users?.length">
                <!-- head -->
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Security Role</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- row 1 -->
                    <tr v-for="user in team.users">
                        <td>{{ user.user.name }}</td>
                        <td>{{ user.user.email }}</td>
                        <td>
                            <div
                                v-if="user.user.roles[0]?.name == 'Admin'"
                                class="badge badge-outline"
                            >
                                {{ user.user.roles[0]?.name }}
                            </div>
                            <div
                                v-else-if="user.user.roles[0]?.name == 'Account Owner'"
                                class="badge badge-success badge-outline"
                            >
                                {{ user.user.roles[0]?.name }}
                            </div>
                            <div v-else class="badge badge-info badge-outline">
                                {{ user.user.roles[0]?.name }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else>No users assigned</div>
        </div>

        <div
            className="form-control col-span-6 lg:col-span-2"
            v-if="team.clubs"
        >
            <h1>Locations</h1>
            <table class="table table-compact w-full">
                <!-- head -->
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- row 1 -->
                    <tr v-for="club in team.clubs">
                        <th>{{ club.location_no }}</th>
                        <td>{{ club.name }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
input {
    @apply input-xs;
}
.field {
    @apply flex flex-row gap-2;
}
</style>

<script>
import { defineComponent } from "vue";
export default defineComponent({
    props: ["team"],
});
</script>
