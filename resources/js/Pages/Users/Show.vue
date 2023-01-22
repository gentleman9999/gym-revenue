<template>
    <LayoutHeader title="Users" />
    <page-toolbar-nav :title="clientName + ' Users'" :links="navLinks" />
    <gym-revenue-crud
        ref="usersCrud"
        base-route="users"
        model-name="User"
        model-key="user"
        :fields="fields"
        :actions="actions"
        :top-actions="topActions"
        :preview-component="UserPreview"
        :edit-component="UserForm"
    >
        <template #filter>
            <beefy-search-filter
                v-model:modelValue="form.search"
                @update:modelValue="
                    handleCrudUpdate('filter', {
                        search: form.search,
                    })
                "
                :filtersActive="filtersActive"
                class="w-full max-w-md mr-4"
            >
                <div class="form-control" v-if="clubs?.length">
                    <label for="club" class="label label-text py-1 text-xs">
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
                    <label for="team" class="label label-text py-1 text-xs">
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
                    <label for="roles" class="label label-text py-1 text-xs">
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
    <confirm
        title="Really Delete?"
        v-if="confirmDelete"
        @confirm="handleConfirmDelete"
        @cancel="confirmDelete = null"
    >
        Are you sure you want to terminate user '{{ confirmDelete.name }}'?
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
import PageToolbarNav from "@/Components/PageToolbarNav.vue";
import UserPreview from "@/Pages/Users/Partials/UserPreview.vue";
import UserForm from "@/Pages/Users/Partials/UserForm.vue";
import BeefySearchFilter from "@/Components/CRUD/BeefySearchFilter.vue";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses, useGymRevForm } from "@/utils";
import DaisyModal from "@/Components/DaisyModal.vue";
import FileManager from "./Partials/FileManager.vue";
export default defineComponent({
    components: {
        BeefySearchFilter,
        LayoutHeader,
        GymRevenueCrud,
        Confirm,
        SimpleSearchFilter,
        PageToolbarNav,
        UserPreview,
        UserForm,
        Multiselect,
        DaisyModal,
        FileManager,
    },
    props: ["filters", "clubs", "teams", "clientName", "potentialRoles"],
    setup(props) {
        const page = usePage();
        const abilities = computed(() => page.props.value.user?.abilities);
        const teamId = computed(() => page.props.value.user?.current_team_id);

        const form = useGymRevForm({});

        const confirmDelete = ref(null);
        const handleClickDelete = (user) => {
            confirmDelete.value = user;
        };
        const handleConfirmDelete = () => {
            Inertia.delete(route("users.terminate", confirmDelete.value.id));
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
                // name: "roles[0].title",
                name: "roles",
                label: "Security Role",
                transform: (roles) => {
                    return roles[0]?.title;
                },
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
                "role",
                {
                    name: "manager",
                    label: "Manager",
                },
                {
                    name: "home_team.name",
                    label: "Home Club",
                },
            ];
        }

        const shouldShowDelete = ({ data }) =>
            (abilities.value.includes("users.trash") ||
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
            terminate: {
                label: "Terminate",
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
        const usersCrud = ref(null);
        const handleCrudUpdate = (key, value) => {
            usersCrud.value.handleCrudUpdate(key, value);
        };
        return {
            confirmDelete,
            fields,
            actions,
            Inertia,
            handleConfirmDelete,
            navLinks,
            UserPreview,
            UserForm,
            multiselectClasses: getDefaultMultiselectTWClasses(),
            topActions,
            handleClickImport,
            importUser,
            closeModals,
            form,
            handleCrudUpdate,
            usersCrud,
        };
    },
});
</script>
