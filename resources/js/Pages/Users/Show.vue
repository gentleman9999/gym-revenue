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
<!--                    <template #trigger>-->
<!--                            <span class="inline-flex rounded-md">-->
<!--                                <button-->
<!--                                    type="button"-->
<!--                                    class="inline-flex items-center px-3 py-2 border border-white text-sm leading-4 font-medium rounded-md bg-white hover:bg-base-100 bg-base-200 focus:outline-none focus:bg-base-100 active:bg-base-100 transition"-->
<!--                                >-->
<!--                                    Test-->

<!--                                    <svg-->
<!--                                        class="ml-2 -mr-0.5 h-4 w-4"-->
<!--                                        xmlns="http://www.w3.org/2000/svg"-->
<!--                                        viewBox="0 0 20 20"-->
<!--                                        fill="currentColor"-->
<!--                                    >-->
<!--                                        <path-->
<!--                                            fill-rule="evenodd"-->
<!--                                            d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"-->
<!--                                            clip-rule="evenodd"-->
<!--                                        />-->
<!--                                    </svg>-->
<!--                                </button>-->
<!--                            </span>-->
<!--                    </template>-->

<!--                    <template #content>-->
<!--                        &lt;!&ndash; Team Management &ndash;&gt;-->
<!--                        Test-->
<!--                    </template>-->
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
    props: ["users", "filters"],
    setup() {
        const {form, reset} = useSearchFilter('users', {
            // foo: 'bar'
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
