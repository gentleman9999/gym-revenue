<template>
    <!-- Teams Dropdown -->
    <jet-dropdown width="60">
        <template #trigger>
            <span class="inline-flex rounded-md">
                <button
                    type="button"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md focus:outline-none transition"
                >
                    Switch Team

                    <svg
                        class="ml-2 -mr-0.5 h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
            </span>
        </template>

        <template #content>
            <div class="w-60">
                <!-- Team Management -->
                <template v-if="$page.props.jetstream.hasTeamFeatures">
                    <!--
                                                <div class="block px-4 py-2 text-xs ">
                                                    Manage Team
                                                </div>
                                                -->

                    <!-- Team Settings
                                                <inertia-link :href="route('teams.show', $page.props.user.current_team)">
                                                    Team Settings
                                                </inertia-link>

                                                <inertia-link :href="route('teams.create')" v-if="$page.props.jetstream.canCreateTeams">
                                                    Create New Team
                                                </inertia-link>

                                                <div class="border-t border-base-100-100"></div>
                                                -->
                    <!-- Team Switcher -->
                    <div class="block px-4 py-2 text-xs">
                        <small>Current Team</small>
                        <small>{{ $page.props.user.current_team.name }}</small>
                    </div>

                    <ul class="menu compact overflow-y-scroll max-h-60">
                        <li
                            v-for="team in $page.props.user.all_teams"
                            :key="team.id"
                        >
                            <inertia-link
                                href="#"
                                @click="switchToTeam(team)"
                                class="flex items-center"
                            >
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
                                {{ team.name }}
                            </inertia-link>
                        </li>
                    </ul>
                </template>
            </div>
        </template>
    </jet-dropdown>
</template>

<script>
import { defineComponent } from "vue";
import JetDropdown from "@/Components/Dropdown";
import { Inertia } from "@inertiajs/inertia";

export default defineComponent({
    components: {
        JetDropdown,
    },
    setup(props) {
        function switchToTeam(team) {
            Inertia.put(
                route("current-team.update"),
                {
                    team_id: team.id,
                },
                {
                    preserveState: false,
                }
            );
        }
        return { switchToTeam };
    },
});
</script>
