<template>
    <div className="grid grid-cols-6 gap-6">
        <div className="form-control col-span-3 lg:col-span-3">
            <h1>Team Name</h1>
            <jet-label htmlFor="name" value="Team Name"/>
            <input
                id="name"
                type="text"
                className="block w-full mt-1"
                :value="team.team.name"
                disabled
            />
        </div>

        <div className="form-control col-span-3 lg:col-span-3">
            <h1>Account</h1>
            <jet-label htmlFor="client_name" value="Client Name"/>
            <input
                v-if="team.client"
                id="client_name"
                type="text"
                className="block w-full mt-1"
                :value="team.client.name"
                disabled
            />
        </div>

        <div className="form-control col-span-6 lg:col-span-6">
            <h1>Users</h1>
            <table class="table table-compact w-full">
                <!-- head -->
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
                </thead>
                <tbody>
                <!-- row 1 -->
                <tr v-for='user in team.users'>
                    <th>{{ user.user.id }}</th>
                    <td>{{ user.user.name }}</td>
                    <td>{{ user.user.email }}</td>
                    <td> <div v-if='user.role == "Admin"' class="badge badge-outline">{{ user.role }}</div>
                        <div v-else-if='user.role == "Account Owner"' class="badge badge-success badge-outline">{{ user.role }}</div>
                        <div v-else class="badge badge-info badge-outline">{{ user.role }}</div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div className="form-control col-span-6 lg:col-span-6">
            <h1>Locations</h1>
            <table class="table table-compact w-full">
                <!-- head -->
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>City/State</th>
                    <th>Zip</th>
                </tr>
                </thead>
                <tbody>
                <!-- row 1 -->
                <tr v-for='club in team.clubs'>
                    <th>{{ club.location_no }}</th>
                    <td>{{ club.name }}</td>
                    <td>{{ club.city }} / {{ club.state }}</td>
                    <td>{{ club.zip }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div className="form-control col-span-3 lg:col-span-3">

        </div>
    </div>


    <br><br>
</template>

<script>
import {defineComponent} from "vue";
import AppLayout from "@/Layouts/AppLayout";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import TeamMemberManager from "@/Pages/Teams/Partials/TeamMemberManager";
import UpdateTeamNameForm from "@/Pages/Teams/Partials/UpdateTeamNameForm";
import TeamForm from "./TeamForm";
import {usePage} from "@inertiajs/inertia-vue3";

export default defineComponent({
    components: {
        AppLayout,
        JetSectionBorder,
        TeamMemberManager,
        UpdateTeamNameForm,
        TeamForm
    },
    props: ["team", "users", "clubs", "client"],
    setup(props, {emit}) {
        const page = usePage();
        let users = props.users;

        return {users};
    },

});
</script>
