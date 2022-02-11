<template>
    <app-layout :title="title">
        <!--        security roles not yet implemented - hide for now-->
        <page-toolbar-nav
            title="Users"
            :links="navLinks"
            v-if="$page.props.user.current_client_id"
        />
        <gym-revenue-crud
            base-route="users"
            model-name="user"
            :fields="fields"
            :resource="users"
            :actions="actions"
        >
            <template #filter>
                <search-filter
                    v-model:modelValue="form.search"
                    class="w-full max-w-md mr-4"
                    @reset="reset"
                >
                    <div class="form-control" v-if="clubs?.length">
                        <span class="label label-text">Club</span>
                        <select class="select" v-model="form.club">
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
                        <span class="label label-text">Team</span>
                        <select class="select" v-model="form.team">
                            <option></option>
                            <option v-for="team in teams" :value="team.id">
                                {{ team.name }}
                            </option>
                        </select>
                    </div>
                </search-filter>
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
import SearchFilter from "@/Components/CRUD/SearchFilter";
import { useSearchFilter } from "@/Components/CRUD/helpers/useSearchFilter";
import PageToolbarNav from "@/Components/PageToolbarNav";

export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
        UserForm,
        Confirm,
        SearchFilter,
        PageToolbarNav,
    },
    props: ["users", "filters", "clubs", "teams"],
    setup() {
        const page = usePage();
        const abilities = computed(() => page.props.value.user?.abilities);
        const teamId = computed(() => page.props.value.user?.current_team_id);
        console.log("page.props.value.users", page.props.value.users);
        console.log("teamId", teamId.value);
        console.log("abilities", abilities.value);

        const { form, reset } = useSearchFilter("users", {
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

        const fields = ["name", "created_at", "updated_at"];

        // const shouldShowDelete = ({ data }) => {
        //     console.log({ability: abilities.value.includes("users.delete")});
        //     abilities.value["users.delete"]
        // }

        const shouldShowDelete = ({ data }) => abilities.value.includes("users.delete") &&
            data.teams?.find((team) => team.id === teamId.value)?.pivot?.role !==
            "Account Owner";

        const shouldShowEdit = ({ data }) => abilities.value.includes("users.update") &&
            data.teams?.find((team) => team.id === teamId.value)?.pivot?.role !==
            "Account Owner";

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
        const navLinks = [
            {
                label: "Security Roles",
                href: route("security-roles"),
                onClick: null,
            },
        ];
        return {
            confirmDelete,
            fields,
            actions,
            Inertia,
            handleConfirmDelete,
            form,
            reset,
            navLinks,
        };
    },
});
</script>
