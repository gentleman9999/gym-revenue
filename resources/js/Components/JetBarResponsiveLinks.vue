<template>
    <div
        class="relative block lg:hidden"
        v-if="$page.props.jetstream.hasTeamFeatures"
    >
        <button
            @click="
                showingSidebarManageTeamsDropdown =
                    !showingSidebarManageTeamsDropdown
            "
            class="flex flex-row items-center w-full px-4 py-2 mt-2 text-sm font-semibold text-left bg-transparent rounded-lg dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:focus:bg-primary dark-mode:hover:bg-primary lg:block hover:bg-base-100 focus:bg-base-100 focus:outline-none focus:shadow-outline"
        >
            <span>Manage Teams</span>
            <svg
                fill="currentColor"
                viewBox="0 0 20 20"
                :class="{
                    'rotate-180': showingSidebarManageTeamsDropdown,
                    'rotate-0': !showingSidebarManageTeamsDropdown,
                }"
                class="inline w-4 h-4 mt-1 ml-1 transition-transform duration-200 transform lg:-mt-1"
            >
                <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                ></path>
            </svg>
        </button>
        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-if="showingSidebarManageTeamsDropdown"
                class="absolute right-0 w-full mt-2 origin-top-right rounded-md shadow-lg z-20"
            >
                <div class="px-2 py-2 bg-base-300 rounded-md shadow">
                    <!-- Team Switcher -->
                    <div class="block px-4 py-2 text-xs">Manage Team</div>
                    <inertia-link
                        :href="
                            route('teams.show', $page.props.user.current_team)
                        "
                        :class="
                            route().current('teams.show')
                                ? 'bg-base-100'
                                : 'bg-transparent'
                        "
                        class="block px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:hover:bg-primary dark-mode:focus:bg-primary dark-mode:focus:text-white dark-mode:hover:text-white lg:mt-0 hover:bg-base-100 focus:bg-base-100 focus:outline-none focus:shadow-outline"
                        >Team Settings</inertia-link
                    >
                    <inertia-link
                        :href="route('teams.create')"
                        :class="
                            route().current('teams.create')
                                ? 'bg-base-100'
                                : 'bg-transparent'
                        "
                        v-if="$page.props.jetstream.canCreateTeams"
                        class="block px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:hover:bg-primary dark-mode:focus:bg-primary dark-mode:focus:text-white dark-mode:hover:text-white lg:mt-0 hover:bg-base-100 focus:bg-base-100 focus:outline-none focus:shadow-outline"
                        >Create New Team</inertia-link
                    >
                    <div class="border-t border-base-100-100"></div>
                    <!-- Team Switcher -->
                    <div class="block px-4 py-2 text-xs">Switch Teams</div>

                    <template
                        v-for="team in $page.props.user.all_teams"
                        :key="team.id"
                    >
                        <form @submit.prevent="switchToTeam(team)">
                            <button
                                class="block px-4 py-2 mt-2 text-sm font-semibold rounded-lg dark-mode:hover:bg-primary dark-mode:focus:bg-primary dark-mode:focus:text-white dark-mode:hover:text-white lg:mt-0 hover:bg-base-100 focus:bg-base-100 focus:outline-none focus:shadow-outline w-full"
                            >
                                <div class="flex items-center">
                                    <svg
                                        v-if="
                                            team.id ==
                                            $page.props.user.current_team_id
                                        "
                                        class="mr-2 h-5 w-5 text-green-400"
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
                                    <div>{{ team.name }}</div>
                                </div>
                            </button>
                        </form>
                    </template>
                </div>
            </div>
        </transition>
    </div>

    <!--    <div v-show="showingSidebarManageAccountDropdown" @click="showingSidebarManageAccountDropdown = false" class="fixed inset-0 h-full w-full z-10" style="display: none;"></div>-->

    <div class="relative block lg:hidden">
        <button
            @click="
                showingSidebarManageAccountDropdown =
                    !showingSidebarManageAccountDropdown
            "
            class="flex flex-row items-center w-full px-4 py-2 mt-2 text-sm font-semibold text-left bg-transparent rounded-lg dark-mode:focus:text-white dark-mode:hover:text-white dark-mode:focus:bg-primary dark-mode:hover:bg-primary lg:block hover:bg-base-100 focus:bg-base-100 focus:outline-none focus:shadow-outline"
        >
            <span>Manage Account</span>
            <svg
                fill="currentColor"
                viewBox="0 0 20 20"
                :class="{
                    'rotate-180': showingSidebarManageAccountDropdown,
                    'rotate-0': !showingSidebarManageAccountDropdown,
                }"
                class="inline w-4 h-4 mt-1 ml-1 transition-transform duration-200 transform lg:-mt-1"
            >
                <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                ></path>
            </svg>
        </button>
        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-if="showingSidebarManageAccountDropdown"
                class="absolute right-0 w-full mt-2 origin-top-right rounded-md shadow-lg z-20"
            >
                <div class="px-2 py-2 bg-base-300 rounded-md shadow">
                    <inertia-link
                        :href="route('profile.show')"
                        :class="
                            route().current('profile.show')
                                ? 'bg-base-100'
                                : 'bg-transparent'
                        "
                        class="block px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:hover:bg-primary dark-mode:focus:bg-primary dark-mode:focus:text-white dark-mode:hover:text-white lg:mt-0 hover:bg-base-100 focus:bg-base-100 focus:outline-none focus:shadow-outline"
                    >
                        Profile
                    </inertia-link>
                    <inertia-link
                        :href="route('settings')"
                        :class="
                            route().current('profile.show')
                                ? 'bg-base-100'
                                : 'bg-transparent'
                        "
                        class="block px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:hover:bg-primary dark-mode:focus:bg-primary dark-mode:focus:text-white dark-mode:hover:text-white lg:mt-0 hover:bg-base-100 focus:bg-base-100 focus:outline-none focus:shadow-outline"
                    >
                        Settings
                    </inertia-link>
                    <!-- @todo - make these dynamic, as some users wont have access -->
                    <inertia-link
                        :href="route('users')"
                        :class="
                            route().current('profile.show')
                                ? 'bg-base-100'
                                : 'bg-transparent'
                        "
                        class="block px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:hover:bg-primary dark-mode:focus:bg-primary dark-mode:focus:text-white dark-mode:hover:text-white lg:mt-0 hover:bg-base-100 focus:bg-base-100 focus:outline-none focus:shadow-outline"
                    >
                        User Management
                    </inertia-link>
                    <inertia-link
                        :href="route('profile.show')"
                        :class="
                            route().current('profile.show')
                                ? 'bg-base-100'
                                : 'bg-transparent'
                        "
                        class="block px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:hover:bg-primary dark-mode:focus:bg-primary dark-mode:focus:text-white dark-mode:hover:text-white lg:mt-0 hover:bg-base-100 focus:bg-base-100 focus:outline-none focus:shadow-outline"
                    >
                        Invoices
                    </inertia-link>

                    <a
                        @click.prevent="
                            openImpersonation($page.props.user.abilities)
                        "
                        :class="
                            route().current('profile.show')
                                ? 'bg-base-100'
                                : 'bg-transparent'
                        "
                        class="block px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:hover:bg-primary dark-mode:focus:bg-primary dark-mode:focus:text-white dark-mode:hover:text-white lg:mt-0 hover:bg-base-100 focus:bg-base-100 focus:outline-none focus:shadow-outline"
                    >
                        Impersonation
                    </a>
                    <inertia-link
                        :href="route('api-tokens.index')"
                        v-if="$page.props.jetstream.hasApiFeatures"
                        :class="
                            route().current('api-tokens.index')
                                ? 'bg-base-100'
                                : 'bg-transparent'
                        "
                        class="block px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:hover:bg-primary dark-mode:focus:bg-primary dark-mode:focus:text-white dark-mode:hover:text-white lg:mt-0 hover:bg-base-100 focus:bg-base-100 focus:outline-none focus:shadow-outline"
                    >
                        API Tokens
                    </inertia-link>
                    <!--                    <inertia-link :href="route('profile.show')" :class="route().current('profile.show') ? 'bg-base-100' : 'bg-transparent'" class="block px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg  dark-mode:hover:bg-primary dark-mode:focus:bg-primary dark-mode:focus:text-white dark-mode:hover:text-white lg:mt-0 hover:bg-base-100 focus:bg-base-100 focus:outline-none focus:shadow-outline">Profile</inertia-link>-->
                    <!--                    <inertia-link :href="route('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures" class="block px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg  dark-mode:hover:bg-primary dark-mode:focus:bg-primary dark-mode:focus:text-white dark-mode:hover:text-white lg:mt-0 hover:bg-base-100 focus:bg-base-100 focus:outline-none focus:shadow-outline">API Tokens</inertia-link>-->
                    <form @submit.prevent="logout">
                        <button
                            class="block px-4 py-2 mt-2 text-sm font-semibold bg-transparent rounded-lg dark-mode:hover:bg-primary dark-mode:focus:bg-primary dark-mode:focus:text-white dark-mode:hover:text-white lg:mt-0 hover:bg-base-100 focus:bg-base-100 focus:outline-none focus:shadow-outline"
                        >
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </transition>
    </div>
    <daisy-modal
        title="Impersonation Mode"
        width="100%"
        overlayTheme="dark"
        modal-theme="dark"
        ref="impModal"
        @close="impVars.showModal = false"
    >
        <list-of-users-to-impersonate
            v-if="impVars.showModal"
            @close="impVars.closeModal()"
        ></list-of-users-to-impersonate>
    </daisy-modal>
    <!--    <div v-show="showingSidebarManageTeamsDropdown" @click="showingSidebarManageTeamsDropdown = false" class="fixed inset-0 h-full w-full z-10" style="display: none;"></div>-->
</template>

<script>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { toastWarning } from "@/utils/createToast";
import DaisyModal from "@/Components/DaisyModal.vue";
import ListOfUsersToImpersonate from "@/Presenters/Impersonation/ListOfUserstoImpersonate.vue";
export default {
    name: "JetBarResponsiveLinks",
    data() {
        return {
            showingSidebarManageAccountDropdown: false,
            showingSidebarManageTeamsDropdown: false,
        };
    },
    components: {
        ListOfUsersToImpersonate,
        DaisyModal,
    },
    methods: {
        switchToTeam(team) {
            this.$inertia.put(
                route("current-team.update"),
                {
                    team_id: team.id,
                },
                {
                    preserveState: false,
                }
            );
        },
        logout() {
            console.log("test");
            this.$inertia.post(route("logout"));
        },
    },
    setup() {
        let impVars = {
            showModal: false,
            closeModal() {
                impModal.value.close();
            },
        };
        const impModal = ref(null);
        const openImpersonation = (abilities) => {
            if (
                abilities.includes("users.impersonate") ||
                abilities.includes("*")
            ) {
                impVars.showModal = true;
                impModal.value.open();
            } else {
                toastWarning();
            }
        };

        const leaveImpersonationMode = () => {
            Inertia.post(route("impersonation.stop", {}));
        };
        return {
            openImpersonation,
            leaveImpersonationMode,
            impModal,
            impVars,
        };
    },
};
</script>
