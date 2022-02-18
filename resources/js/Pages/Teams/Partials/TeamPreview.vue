<template>
    <div class="grid grid-cols-6 gap-6">
        <div class="form-control col-span-6 lg:col-span-3">
            <jet-label for="name" value="Team Name"/>
            <input
                id="name"
                type="text"
                class="block w-full mt-1"
                :value="team.team.name"
                disabled
            />
        </div>

    </div>
</template>

<script>
import { defineComponent } from "vue";
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
    props: ["team", "users"],
    setup(props, {emit}) {
        const page = usePage();
        let users = props.users;

        console.log(users)
        return {users};
    },

});
</script>
