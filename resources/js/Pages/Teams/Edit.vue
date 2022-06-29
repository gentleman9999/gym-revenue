<template>
    <ModalableWrapper>
        <LayoutHeader title="Team Settings">
            <h2 class="font-semibold text-xl leading-tight">Team Settings</h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <team-form :team="team" />

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
        </template>
    </ModalableWrapper>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import TeamMemberManager from "@/Pages/Teams/Partials/TeamMemberManager";
import UpdateTeamNameForm from "@/Pages/Teams/Partials/UpdateTeamNameForm";
import TeamForm from "./Partials/TeamForm";
import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    props: ["team", "availableRoles", "permissions", "users", "availableUsers"],

    components: {
        LayoutHeader,
        JetSectionBorder,
        TeamMemberManager,
        UpdateTeamNameForm,
        TeamForm,
        ModalableWrapper,
        ModalSlot,
    },
});
</script>
