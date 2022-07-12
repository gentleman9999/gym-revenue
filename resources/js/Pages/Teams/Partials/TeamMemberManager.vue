<template>
    <div>
        <div v-if="userPermissions.canAddTeamMembers">
            <jet-section-border />

            <!-- Add Team Member -->
            <jet-form-section @submitted="addTeamMember">
                <template #title> Add Team Member</template>

                <template #description>
                    Add a new team member to your team, allowing them to
                    collaborate with you.
                </template>

                <template #form>
                    <div class="col-span-6">
                        <div class="max-w-xl text-sm">
                            Please provide the email address of the person you
                            would like to add to this team.
                        </div>
                    </div>

                    <!-- Member Emails -->
                    <div class="col-span-6 sm:col-span-4">
                        <jet-label for="emails" value="Emails" />
                        <multiselect
                            v-model="addTeamMemberForm.emails"
                            id="emails"
                            type="email"
                            mode="tags"
                            :close-on-select="false"
                            :create-option="true"
                            :options="availableUsersToJoinTeamOptions"
                            :classes="multiselectClasses"
                            class="h-full"
                        />
                        <jet-input-error
                            :message="addTeamMemberForm.errors.emails"
                            class="mt-2"
                        />
                    </div>
                </template>

                <template #actions>
                    <jet-action-message
                        :on="addTeamMemberForm.recentlySuccessful"
                        class="mr-3"
                    >
                        Added.
                    </jet-action-message>

                    <Button
                        :class="{ 'opacity-25': addTeamMemberForm.processing }"
                        :disabled="
                            addTeamMemberForm.processing ||
                            !addTeamMemberForm.isDirty
                        "
                    >
                        Add
                    </Button>
                </template>
            </jet-form-section>
        </div>

        <div
            v-if="
                team.team_invitations.length > 0 &&
                userPermissions.canAddTeamMembers
            "
        >
            <jet-section-border />

            <!-- Team Member Invitations -->
            <jet-action-section class="mt-10 sm:mt-0">
                <template #title> Pending Team Invitations</template>

                <template #description>
                    These people have been invited to your team and have been
                    sent an invitation email. They may join the team by
                    accepting the email invitation.
                </template>

                <!-- Pending Team Member Invitation List -->
                <template #content>
                    <div class="space-y-6">
                        <div
                            class="flex items-center justify-between"
                            v-for="invitation in team.team_invitations"
                            :key="invitation.id"
                        >
                            <div class="">{{ invitation.email }}</div>

                            <div class="flex items-center">
                                <!-- Cancel Team Invitation -->
                                <button
                                    class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"
                                    @click="cancelTeamInvitation(invitation)"
                                    v-if="userPermissions.canRemoveTeamMembers"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </jet-action-section>
        </div>

        <div v-if="team.users.length > 0">
            <jet-section-border />

            <!-- Manage Team Members -->
            <jet-action-section class="mt-10 sm:mt-0">
                <template #title> Team Members</template>

                <template #description>
                    All of the people that are part of this team.
                </template>

                <!-- Team Member List -->
                <template #content>
                    <div class="space-y-6">
                        <div
                            class="flex items-center justify-between"
                            v-for="user in team.users.filter(
                                (user) => user.id !== team.owner?.id
                            )"
                            :key="user.id"
                        >
                            <div class="flex items-center">
                                <img
                                    class="w-8 h-8 rounded-full"
                                    :src="user.profile_photo_url"
                                    :alt="user.name"
                                />
                                <div class="ml-4">{{ user.name }}</div>
                            </div>

                            <div class="flex items-center">
                                <!-- Manage Team Member Role -->
                                <button
                                    class="ml-2 text-sm underline"
                                    @click="manageRole(user)"
                                    v-if="
                                        userPermissions.canAddTeamMembers &&
                                        availableRoles.length
                                    "
                                >
                                    {{ displayableRole(user.membership.role) }}
                                </button>

                                <div
                                    class="ml-2 text-sm"
                                    v-else-if="availableRoles.length"
                                >
                                    {{ displayableRole(user.membership.role) }}
                                </div>

                                <!-- Leave Team -->
                                <button
                                    class="cursor-pointer ml-6 text-sm text-red-500"
                                    @click="confirmLeavingTeam"
                                    v-if="$page.props.user.id === user.id"
                                >
                                    Leave
                                </button>

                                <!-- Remove Team Member -->
                                <button
                                    class="cursor-pointer ml-6 text-sm text-red-500"
                                    @click="confirmTeamMemberRemoval(user)"
                                    v-else-if="
                                        userPermissions.canRemoveTeamMembers
                                    "
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </jet-action-section>
        </div>

        <!-- Role Management Modal -->
        <jet-dialog-modal
            :show="currentlyManagingRole"
            @close="currentlyManagingRole = false"
        >
            <template #title> Manage Role</template>

            <template #content>
                <div v-if="managingRoleFor">
                    <div
                        class="relative z-0 mt-1 border border-base-100 rounded-lg cursor-pointer"
                    >
                        <button
                            type="button"
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                            :class="{
                                'border-t border-base-100 rounded-t-none':
                                    i > 0,
                                'rounded-b-none':
                                    i !==
                                    Object.keys(availableRoles).length - 1,
                            }"
                            @click="updateRoleForm.role = role.key"
                            v-for="(role, i) in availableRoles"
                            :key="role.key"
                        >
                            <div
                                :class="{
                                    'opacity-50':
                                        updateRoleForm.role &&
                                        updateRoleForm.role !== role.key,
                                }"
                            >
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div
                                        class="text-sm"
                                        :class="{
                                            'font-semibold':
                                                updateRoleForm.role ===
                                                role.key,
                                        }"
                                    >
                                        {{ role.title }}
                                    </div>

                                    <svg
                                        v-if="updateRoleForm.role === role.key"
                                        class="ml-2 h-5 w-5 text-green-400"
                                        fill="none"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        ></path>
                                    </svg>
                                </div>

                                <!-- Role Description -->
                                <div class="mt-2 text-xs">
                                    {{ role.description }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click="currentlyManagingRole = false">
                    Cancel
                </jet-secondary-button>

                <Button
                    class="ml-2"
                    @click="updateRole"
                    :class="{ 'opacity-25': updateRoleForm.processing }"
                    :disabled="updateRoleForm.processing"
                >
                    Save
                </Button>
            </template>
        </jet-dialog-modal>

        <!-- Leave Team Confirmation Modal -->
        <jet-confirmation-modal
            :show="confirmingLeavingTeam"
            @close="confirmingLeavingTeam = false"
        >
            <template #title> Leave Team</template>

            <template #content>
                Are you sure you would like to leave this team?
            </template>

            <template #footer>
                <jet-secondary-button @click="confirmingLeavingTeam = false">
                    Cancel
                </jet-secondary-button>

                <button
                    class="error ml-2"
                    @click="leaveTeam"
                    :class="{ 'opacity-25': leaveTeamForm.processing }"
                    :disabled="leaveTeamForm.processing"
                >
                    Leave
                </button>
            </template>
        </jet-confirmation-modal>

        <!-- Remove Team Member Confirmation Modal -->
        <jet-confirmation-modal
            :show="teamMemberBeingRemoved"
            @close="teamMemberBeingRemoved = null"
        >
            <template #title> Remove Team Member</template>

            <template #content>
                Are you sure you would like to remove this person from the team?
            </template>

            <template #footer>
                <jet-secondary-button @click="teamMemberBeingRemoved = null">
                    Cancel
                </jet-secondary-button>

                <button
                    class="error ml-2"
                    @click="removeTeamMember"
                    :class="{ 'opacity-25': removeTeamMemberForm.processing }"
                    :disabled="removeTeamMemberForm.processing"
                >
                    Remove
                </button>
            </template>
        </jet-confirmation-modal>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { computed } from "@vue/reactivity";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetActionSection from "@/Jetstream/ActionSection.vue";
import Button from "@/Components/Button.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";

import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import Multiselect from "@vueform/multiselect";

import { getDefaultMultiselectTWClasses, useGymRevForm } from "@/utils";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";

const props = defineProps({
    team: {
        type: Object,
    },
    availableRoles: {
        type: Array,
        default: [],
    },
    availableUsers: {
        type: Array,
        default: [],
    },
    userPermissions: {
        type: Array,
        default: [],
    },
    users: {
        type: Array,
        default: [],
    },
});

const addTeamMemberForm = useGymRevForm({
    emails: [],
});

const updateRoleForm = useGymRevForm({
    role: null,
});

const leaveTeamForm = useGymRevForm();
const removeTeamMemberForm = useGymRevForm();

const currentlyManagingRole = ref(false);
const managingRoleFor = ref(null);
const confirmingLeavingTeam = ref(false);
const teamMemberBeingRemoved = ref(null);
const multiselectClasses = ref(getDefaultMultiselectTWClasses());
async function addTeamMember() {
    addTeamMemberForm.post(route("team-member.store", team));
}
function cancelTeamInvitation(invitation) {
    Inertia.delete(route("team-invitations.destroy", invitation), {
        preserveScroll: true,
    });
}

function manageRole(teamMember) {
    managingRoleFor.value = teamMember;
    updateRoleForm.role = teamMember.membership.role;
    currentlyManagingRole.value = true;
}

function updateRole() {
    updateRoleForm.put(
        route("team-members.update", [props.team, managingRoleFor.value]),
        {
            preserveScroll: true,
            onSuccess: () => (currentlyManagingRole.value = false),
        }
    );
}

function confirmLeavingTeam() {
    confirmingLeavingprops.team = true;
}

const page = usePage();
function leaveTeam() {
    leaveTeamForm.delete(
        route("team-members.destroy", [props.team, page.props.user])
    );
}

function confirmTeamMemberRemoval(teamMember) {
    console.log({ teamMember });
    teamMemberBeingRemoved.value = teamMember;
}

function removeTeamMember() {
    removeTeamMemberForm.delete(
        route("team-members.destroy", [
            props.team,
            teamMemberBeingRemoved.value,
        ]),
        {
            errorBag: "removeTeamMember",
            preserveScroll: true,
            preserveState: true,
            // onSuccess: () => (teamMemberBeingRemoved.value = null),
            onSuccess: () => {
                teamMemberBeingRemoved.value = null;
            },
        }
    );
}

function displayableRole(role) {
    return props.availableRoles.find((r) => r.key === role)?.name;
}
const userIds = computed({
    get() {
        return props.users.map.value?.map((user) => user.id) || [];
    },
});

const availableUsersToJoinTeam = computed({
    get() {
        const availableUsersToJoinTeam =
            props.availableUsers?.filter(
                (user) => !userIds.value.includes(user.id)
            ) || [];
        return availableUsersToJoinTeam || [];
    },
});

const availableUsersToJoinTeamOptions = computed({
    get() {
        let options = [];
        if (availableUsersToJoinTeam?.map) {
            options = availableUsersToJoinTeam.map(({ email, name }) => ({
                label: `${email} (${name})`,
                value: email,
            }));
        }
        return options;
    },
});
</script>
