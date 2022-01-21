<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">User Management</h2>
        </template>
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
                    <div class="form-control">
                        <span class="label label-text">Club</span>
                        <select class="select" v-model="form.club">
                            <option></option>
                            <option v-for="club in clubs" :value="club.gymrevenue_id">{{club.name}}</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <span class="label label-text">Team</span>
                        <select class="select" v-model="form.team">
                            <option></option>
                            <option v-for="team in teams" :value="team.id">{{team.name}}</option>
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
            Are you sure you want to delete user '{{ confirmDelete.name }}'? This action is permanent, and cannot be
            undone.
        </confirm>
    </app-layout>
</template>

<script>
import {defineComponent, ref} from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import UserForm from "./Partials/UserForm";
import {Inertia} from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm";
import SearchFilter from "@/Components/CRUD/SearchFilter";
import {useSearchFilter} from "@/Components/CRUD/helpers/useSearchFilter";

export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
        UserForm,
        Confirm,
        SearchFilter
    },
    props: ["users", "filters", "clubs", "teams"],
    setup() {
        const {form, reset} = useSearchFilter('users', {
            team: null,
            club: null
        });
        const confirmDelete = ref(null);
        const handleClickDelete = (user) => {
            confirmDelete.value = user;
        };
        const handleConfirmDelete = () => {
            Inertia.delete(route("users.delete", confirmDelete.value.id));
            confirmDelete.value = null;
        };

        const fields = [
            "name",
            "created_at",
            "updated_at",
        ];

        const actions = {
            trash: false,
            restore: false,
            delete: {
                label: 'Delete',
                handler: ({data}) => handleClickDelete(data)
            }
        }
        return {
            confirmDelete,
            fields,
            actions,
            Inertia,
            handleConfirmDelete,
            form,
            reset
        };
    },
});
</script>
