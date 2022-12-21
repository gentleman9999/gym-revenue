<template>
    <div class="grid grid-cols-6 gap-6">
        <div class="field col-span-6 md:col-span-3">
            <label>Team Name:</label>
            <div class="data">
                {{ team.team.name }}
            </div>
        </div>

        <div class="field col-span-6 md:col-span-3" v-if="team.client">
            <label>Client Name:</label>
            <div class="data" v-if="team.client">
                {{ team.client.name }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-6 gap-6 mt-4">
        <div class="form-control col-span-6 lg:col-span-4">
            <h1>Users</h1>
            <div class="w-full overflow-x-scroll">
                <table class="table table-compact" v-if="team.users?.length">
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
                        <tr v-for="user in team.users" :key="user.id">
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
                                    v-else-if="
                                        user.user.roles[0]?.name ==
                                        'Account Owner'
                                    "
                                    class="badge badge-success badge-outline"
                                >
                                    {{ user.user.roles[0]?.name }}
                                </div>
                                <div
                                    v-else
                                    class="badge badge-info badge-outline"
                                >
                                    {{ user.user.roles[0]?.name }}
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-else>No users assigned</div>
            </div>
        </div>

        <div class="form-control col-span-6 lg:col-span-2" v-if="team.clubs">
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
                    <tr v-for="club in team.clubs" :key="club.id">
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
.table th:first-child {
    @apply static;
}
</style>

<script setup>
const props = defineProps({
    team: {
        type: Object,
    },
});
</script>
