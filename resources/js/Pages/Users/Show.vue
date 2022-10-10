<template>
    <LayoutHeader title="Users" />
    <page-toolbar-nav :title="clientName + ' Users'" :links="navLinks" />
    <ApolloQuery :query="(gql) => user_query" :variables="param">
        <template v-slot="{ result: { data } }">
            <gym-revenue-crud
                v-if="data"
                base-route="users"
                model-name="User"
                model-key="user"
                :fields="fields"
                :resource="getUsers(data)"
                :actions="actions"
                :top-actions="topActions"
                :preview-component="UserPreview"
                @update-page="(value) => (param = { ...param, page: value })"
            >
                <template #filter>
                    <beefy-search-filter
                        v-model:modelValue="form.search"
                        :filtersActive="filtersActive"
                        class="w-full max-w-md mr-4"
                        @reset="reset"
                        @clear-filters="clearFilters"
                        @clear-search="clearSearch"
                    >
                        <div class="form-control" v-if="clubs?.length">
                            <label
                                for="club"
                                class="label label-text py-1 text-xs"
                            >
                                Club
                            </label>
                            <select
                                id="club"
                                class="mt-1 w-full form-select"
                                v-model="form.club"
                            >
                                <option></option>
                                <option
                                    v-for="club in clubs"
                                    :key="club.gymrevenue_id"
                                    :value="club.gymrevenue_id"
                                >
                                    {{ club.name }}
                                </option>
                            </select>
                        </div>
                        <div class="form-control" v-if="teams?.length">
                            <label
                                for="team"
                                class="label label-text py-1 text-xs"
                            >
                                Team
                            </label>
                            <select
                                id="team"
                                class="mt-1 w-full form-select"
                                v-model="form.team"
                            >
                                <option></option>
                                <option
                                    v-for="team in teams"
                                    :value="team.id"
                                    :key="team.id"
                                >
                                    {{ team.name }}
                                </option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label
                                for="roles"
                                class="label label-text py-1 text-xs"
                            >
                                Security Role:
                            </label>
                            <select
                                id="roles"
                                class="mt-1 w-full form-select"
                                v-model="form.roles"
                            >
                                <option></option>
                                <option
                                    v-for="role in potentialRoles"
                                    :value="role.id"
                                    :key="role.id"
                                >
                                    {{ role.title }}
                                </option>
                            </select>
                        </div>
                    </beefy-search-filter>
                </template>
            </gym-revenue-crud>
        </template>
    </ApolloQuery>

    <confirm
        title="Really Delete?"
        v-if="confirmDelete"
        @confirm="handleConfirmDelete"
        @cancel="confirmDelete = null"
    >
        Are you sure you want to delete user '{{ confirmDelete.name }}'? This
        action is permanent, and cannot be undone.
    </confirm>

    <daisy-modal
        ref="importUser"
        id="importUser"
        class="lg:max-w-5xl bg-base-300"
    >
        <file-manager @submitted="closeModals" />
    </daisy-modal>
</template>

<script>
import { defineComponent, ref, computed, watch } from "vue";
import * as _ from "lodash";
import { usePage } from "@inertiajs/inertia-vue3";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm.vue";
import SimpleSearchFilter from "@/Components/CRUD/SimpleSearchFilter.vue";
import { useSearchFilter } from "@/Components/CRUD/helpers/useSearchFilter";
import PageToolbarNav from "@/Components/PageToolbarNav.vue";
import UserPreview from "@/Pages/Users/Partials/UserPreview.vue";
import BeefySearchFilter from "@/Components/CRUD/BeefySearchFilter.vue";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses } from "@/utils";
import DaisyModal from "@/Components/DaisyModal.vue";
import FileManager from "./Partials/FileManager.vue";

import gql from "graphql-tag";
export default defineComponent({
    components: {
        BeefySearchFilter,
        LayoutHeader,
        GymRevenueCrud,
        Confirm,
        SimpleSearchFilter,
        PageToolbarNav,
        UserPreview,
        Multiselect,
        DaisyModal,
        FileManager,
    },
    props: ["filters", "clubs", "teams", "clientName", "potentialRoles"],
    setup(props) {
        const param = ref({
            page: 1,
        });
        const user_query = gql`
            query Users($page: Int) {
                users(page: $page) {
                    data {
                        id
                        name
                        email
                        manager
                        role
                        home_team: default_team {
                            name
                        }
                    }
                    pagination: paginatorInfo {
                        current_page: currentPage
                        last_page: lastPage
                        from: firstItem
                        to: lastItem
                        per_page: perPage
                        total
                    }
                }
            }
        `;

        const getUsers = (data) => {
            return _.cloneDeep(data.users);
        };

        const page = usePage();
        const abilities = computed(() => page.props.value.user?.abilities);
        const teamId = computed(() => page.props.value.user?.current_team_id);

        const { form, reset, clearFilters, clearSearch, filtersActive } =
            useSearchFilter("users", {
                team: null,
                club: null,
            });
        const confirmDelete = ref(null);
        const handleClickDelete = (user) => {
            confirmDelete.value = user;
        };
        const handleConfirmDelete = () => {
            Inertia.delete(route("users.delete", confirmDelete.value.id));
            confirmDelete.value = null;
        };

        const importUser = ref();
        const handleClickImport = () => {
            importUser.value.open();
        };

        const closeModals = () => {
            importUser.value.close();
        };

        let fields = [
            "name",
            "email",
            {
                name: "role",
                label: "Security Role",
            },
            {
                name: "manager",
                label: "Manager",
            },
            {
                name: "home_team.name",
                label: "Home Team",
            },
        ];
        if (page.props.value.user.current_team.isClientTeam) {
            fields = [
                "name",
                "email",
                {
                    name: "home_location.name",
                    label: "Home Club",
                    // transform: (data) => data?.home_location?.name,
                },
                "role",
                {
                    name: "manager",
                    label: "Manager",
                },
                "home_team.name",
            ];
        }

        const shouldShowDelete = ({ data }) =>
            (abilities.value.includes("users.delete") ||
                abilities.value.includes("*")) &&
            data?.teams?.find((team) => team.id === teamId.value)?.pivot
                ?.role !== "Account Owner";

        const shouldShowEdit = ({ data }) =>
            (abilities.value.includes("users.update") ||
                abilities.value.includes("*")) &&
            data?.teams?.find((team) => team.id === teamId.value)?.pivot
                ?.role !== "Account Owner";

        const actions = {
            trash: false,
            restore: false,
            delete: {
                label: "Delete",
                handler: ({ data }) => handleClickDelete(data),
                shouldRender: shouldShowDelete,
            },
            edit: {
                shouldRender: shouldShowEdit,
            },
        };

        let navLinks = [
            {
                label: "Users",
                href: route("users"),
                onClick: null,
                active: true,
            },
        ];

        if (page.props.value.user.current_team.isClientTeam) {
            navLinks.push({
                label: "Security Roles",
                href: route("roles"),
                onClick: null,
                active: false,
            });
        }
        if (page.props.value.user.current_team.isClientTeam) {
            navLinks.push({
                label: "Departments",
                href: route("departments"),
                onClick: null,
                active: false,
            });
        }
        if (page.props.value.user.current_team.isClientTeam) {
            navLinks.push({
                label: "Positions",
                href: route("positions"),
                onClick: null,
                active: false,
            });
        }

        const topActions = {
            import: {
                label: "Import",
                handler: handleClickImport,
                class: "btn-primary",
            },
        };

        return {
            confirmDelete,
            fields,
            actions,
            Inertia,
            handleConfirmDelete,
            form,
            reset,
            clearFilters,
            clearSearch,
            navLinks,
            UserPreview,
            multiselectClasses: getDefaultMultiselectTWClasses(),
            topActions,
            handleClickImport,
            importUser,
            closeModals,
            filtersActive,
            user_query,
            param,
            getUsers,
        };
    },
});
</script>
