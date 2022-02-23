<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Team Management</h2>
        </template>
        <gym-revenue-crud
            base-route="teams"
            model-name="Team"
            model-key="team"
            :fields="fields"
            :resource="teams"
            :actions="actions"
            :preview-component="TeamPreview"
        >
            <template #filter>
                <simple-search-filter
                    v-model:modelValue="form.search"
                    class="w-full max-w-md mr-4"
                    @reset="reset"
                >
                    <div class="form-control" v-if="clubs?.length">
                        <span class="label label-text">Club</span>
                        <select class="select" v-model="form.club">
                            <option></option>
                            <option v-for="club in clubs" :value="club.gymrevenue_id">{{club.name}}</option>
                        </select>
                    </div>
                </simple-search-filter>
            </template>
        </gym-revenue-crud>
        <confirm
            title="Really Delete?"
            v-if="confirmDelete"
            @confirm="handleConfirmDelete"
            @cancel="confirmDelete = null"
        >
            Are you sure you want to delete team '{{ confirmDelete.name }}'? This action is permanent, and cannot be
            undone.
        </confirm>
    </app-layout>
</template>

<script>
import {defineComponent, ref, onMounted, computed} from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import {Inertia} from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm";
import SimpleSearchFilter from "@/Components/CRUD/SimpleSearchFilter";import {useSearchFilter} from "@/Components/CRUD/helpers/useSearchFilter";
import TeamPreview from "@/Pages/Teams/Partials/TeamPreview";
import {preview} from "@/Components/CRUD/helpers/previewData";
import {usePage} from "@inertiajs/inertia-vue3";

export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
        Confirm,
        SimpleSearchFilter,
        TeamPreview
    },
    props: ["filters", "clubs", "teams", "preview"],
    setup(props) {
        const baseRoute = "teams";
	const page = usePage();
        const abilities = computed(() => page.props.value.user?.abilities);
        const {form, reset} = useSearchFilter('teams', {
            club: null
        });
        const confirmDelete = ref(null);
        const handleClickDelete = (user) => {
            confirmDelete.value = user;
        };
        const handleConfirmDelete = () => {
            Inertia.delete(route("teams.delete", confirmDelete.value.id));
            confirmDelete.value = null;
        };

        const shouldShowDelete = ({ data }) => (abilities.value.includes("locations.delete") || abilities.value.includes("*")) && !data.default_team;

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
                handler: ({data}) => handleClickDelete(data),
                shouldRender: shouldShowDelete,
            }
        }

        onMounted(() => {
            if (props.preview) {
              preview(baseRoute, props.preview);
            }
        })

        return {
            confirmDelete,
            fields,
            actions,
            Inertia,
            handleConfirmDelete,
            form,
            reset,
            TeamPreview,
            baseRoute
        };
    },
});
</script>
