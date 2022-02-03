<template>
    <app-layout title="Team Settings">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Team Settings</h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <team-form
                    :team="team"
                />

                <team-member-manager
                    class="mt-10 sm:mt-0"
                    :team="team"
                    :available-roles="availableRoles"
                    :user-permissions="permissions"
                    :users="users"
                    :availableUsers="availableUsers"
                />

<!--                <template-->
<!--                    v-if="permissions.canDeleteTeam && !team.personal_team"-->
<!--                >-->
<!--                    <jet-section-border />-->

<!--                    <delete-team-form class="mt-10 sm:mt-0" :team="team" />-->
<!--                </template>-->
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import DeleteTeamForm from "@/Pages/Teams/Partials/DeleteTeamForm";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import TeamMemberManager from "@/Pages/Teams/Partials/TeamMemberManager";
import UpdateTeamNameForm from "@/Pages/Teams/Partials/UpdateTeamNameForm";
import TeamForm from "./Partials/TeamForm";

export default defineComponent({
    props: ["team", "availableRoles", "permissions", "users", "availableUsers"],

    components: {
        AppLayout,
        DeleteTeamForm,
        JetSectionBorder,
        TeamMemberManager,
        UpdateTeamNameForm,
        TeamForm
    },
});
</script>
