<template>
    <app-layout :title="title">
        <!--        security roles not yet implemented - hide for now-->
        <page-toolbar-nav :title="clientName + ' Users'" :links="navLinks" />
        <gym-revenue-crud
            base-route="users"
            model-name="User"
            model-key="user"
            :fields="fields"
            :resource="users"
            :actions="actions"
            :preview-component="UserPreview"
        >
            <template #filter>
                <beefy-search-filter
                    v-model:modelValue="form.search"
                    class="w-full max-w-md mr-4"
                    @reset="reset"
                    @clear-filters="clearFilters"
                    @clear-search="clearSearch"
                >
                    <div class="form-control" v-if="clubs?.length">
                        <label
                            for="club"
                            class="label label-text py-1 text-xs text-gray-400"
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
                                :value="club.gymrevenue_id"
                            >
                                {{ club.name }}
                            </option>
                        </select>
                    </div>
                    <div class="form-control" v-if="teams?.length">
                        <label
                            for="team"
                            class="label label-text py-1 text-xs text-gray-400"
                        >
                            Team
                        </label>
                        <select
                            id="team"
                            class="mt-1 w-full form-select"
                            v-model="form.team"
                        >
                            <option></option>
                            <option v-for="team in teams" :value="team.id">
                                {{ team.name }}
                            </option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label
                            for="roles"
                            class="label label-text py-1 text-xs text-gray-400"
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
                            >
                                {{ role.title }}
                            </option>
                        </select>
                    </div>
                </beefy-search-filter>
            </template>
        </gym-revenue-crud>
        <confirm
            title="Really Delete?"
            v-if="confirmDelete"
            @confirm="handleConfirmDelete"
            @cancel="confirmDelete = null"
        >
            Are you sure you want to delete user '{{ confirmDelete.name }}'?
            This action is permanent, and cannot be undone.
        </confirm>
    </app-layout>
</template>

<script>
import { defineComponent, ref, computed } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import UserForm from "./Partials/UserForm";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm";
import SimpleSearchFilter from "@/Components/CRUD/SimpleSearchFilter";
import { useSearchFilter } from "@/Components/CRUD/helpers/useSearchFilter";
import PageToolbarNav from "@/Components/PageToolbarNav";
import UserPreview from "@/Pages/Users/Partials/UserPreview";
import BeefySearchFilter from "@/Components/CRUD/BeefySearchFilter";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses } from "@/utils";

export default defineComponent({
    components: {
        BeefySearchFilter,
        AppLayout,
        GymRevenueCrud,
        UserForm,
        Confirm,
        SimpleSearchFilter,
        PageToolbarNav,
        UserPreview,
        Multiselect,
    },
    props: [
        "users",
        "filters",
        "clubs",
        "teams",
        "clientName",
        "potentialRoles",
    ],
    setup(props) {
        const page = usePage();
        const abilities = computed(() => page.props.value.user?.abilities);
        const teamId = computed(() => page.props.value.user?.current_team_id);
        console.log("page.props.value.users", page.props.value.users);
        console.log("teamId", teamId.value);
        console.log("abilities", abilities.value);

        const { form, reset, clearFilters, clearSearch } = useSearchFilter(
            "users",
            {
                team: null,
                club: null,
            }
        );
        const confirmDelete = ref(null);
        const handleClickDelete = (user) => {
            confirmDelete.value = user;
        };
        const handleConfirmDelete = () => {
            Inertia.delete(route("users.delete", confirmDelete.value.id));
            confirmDelete.value = null;
        };

        let fields = [
            "name",
            "email",
            {
                name: "role",
                label: "Security Role",
            },
            {
                name: "classification",
                label: "Classification",
                transform: (data) => data?.value,
            },
            {
                name: "is_manager",
                label: "Manager",
                transform: (data) => data?.value,
            },
            "home_team",
        ];
        if (page.props.value.user.current_client_id) {
            fields = [
                "name",
                "email",
                {
                    name: "home_club_name",
                    label: "Home Club",
                },
                "role",
                {
                    name: "classification",
                    label: "Classification",
                    transform: (data) => data?.value,
                },
                {
                    name: "is_manager",
                    label: "Manager",
                    transform: (data) => data.value,
                },
                "home_team",
            ];
        }

        // const shouldShowDelete = ({ data }) => {
        //     console.log({ability: abilities.value.includes("users.delete")});
        //     abilities.value["users.delete"]
        // }

        const shouldShowDelete = ({ data }) =>
            (abilities.value.includes("users.delete") ||
                abilities.value.includes("*")) &&
            data.teams?.find((team) => team.id === teamId.value)?.pivot
                ?.role !== "Account Owner";

        const shouldShowEdit = ({ data }) =>
            (abilities.value.includes("users.update") ||
                abilities.value.includes("*")) &&
            data.teams?.find((team) => team.id === teamId.value)?.pivot
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

        if (page.props.value.user.current_client_id) {
            navLinks.push({
                label: "Roles",
                href: route("roles"),
                onClick: null,
                active: false,
            });
        }
        if (page.props.value.user.current_client_id) {
            navLinks.push({
                label: "Classification",
                href: route("classifications"),
                onClick: null,
                active: false,
            });
        }

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
        };
    },
});
</script>
