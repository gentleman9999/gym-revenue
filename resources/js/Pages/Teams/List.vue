<template>
    <LayoutHeader title="Teams">
        <h2 class="font-semibold text-xl leading-tight">Team Management</h2>
    </LayoutHeader>
    <ApolloQuery :query="(gql) => team_query" :variables="param">
        <template v-slot="{ result: { data } }">
            <gym-revenue-crud
                v-if="data"
                base-route="teams"
                model-name="Team"
                model-key="team"
                :fields="fields"
                :resource="getTeams(data)"
                :actions="actions"
                :preview-component="TeamPreview"
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
                        <div class="form-control">
                            <label
                                for="users"
                                class="label label-text py-1 text-xs"
                            >
                                Users:
                            </label>
                            <multiselect
                                v-model="form.users"
                                class="py-2"
                                id="users"
                                mode="tags"
                                :close-on-select="false"
                                :create-option="true"
                                :options="
                                    this.$page.props.potentialUsers.map(
                                        (user) => ({
                                            label: user.name,
                                            value: user.id,
                                        })
                                    )
                                "
                                :classes="multiselectClasses"
                            />
                        </div>

                        <div class="form-control" v-if="clubs?.length">
                            <label
                                for="club"
                                class="label label-text py-1 text-xs"
                            >
                                Club
                            </label>
                            <select
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
        Are you sure you want to delete team '{{ confirmDelete.name }}'? This
        action is permanent, and cannot be undone.
    </confirm>
</template>

<script>
import { defineComponent, ref, onMounted, computed } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm.vue";
import TeamPreview from "@/Pages/Teams/Partials/TeamPreview.vue";
import { preview } from "@/Components/CRUD/helpers/previewData";
import { usePage } from "@inertiajs/inertia-vue3";
import { useSearchFilter } from "@/Components/CRUD/helpers/useSearchFilter";
import BeefySearchFilter from "@/Components/CRUD/BeefySearchFilter.vue";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses } from "@/utils";
import gql from "graphql-tag";

export default defineComponent({
    components: {
        LayoutHeader,
        GymRevenueCrud,
        Confirm,
        BeefySearchFilter,
        TeamPreview,
        Multiselect,
    },
    props: ["filters", "clubs", "preview", "potentialUsers"],
    setup(props) {
        const baseRoute = "teams";
        const page = usePage();
        const abilities = computed(() => page.props.value.user?.abilities);
        const { form, reset, clearFilters, clearSearch, filtersActive } =
            useSearchFilter("teams", {
                club: null,
            });
        const confirmDelete = ref(null);
        const handleClickDelete = (user) => {
            confirmDelete.value = user;
        };
        const handleConfirmDelete = () => {
            Inertia.delete(route("teams.delete", confirmDelete.value.id));
            confirmDelete.value = null;
        };

        const shouldShowDelete = ({ data }) =>
            (abilities.value.includes("locations.delete") ||
                abilities.value.includes("*")) &&
            !data?.default_team;

        const fields = ["name", "created_at", "updated_at"];

        const actions = {
            trash: false,
            restore: false,
            delete: {
                label: "Delete",
                handler: ({ data }) => handleClickDelete(data),
                shouldRender: shouldShowDelete,
            },
        };

        onMounted(() => {
            if (props.preview) {
                preview(baseRoute, props.preview);
            }
        });
        const param = ref({
            page: 1,
        });
        const team_query = gql`
            query Teams($page: Int) {
                teams(page: $page) {
                    data {
                        id
                        name
                        created_at
                        updated_at
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

        const getTeams = (data) => {
            return _.cloneDeep(data.teams);
        };

        return {
            confirmDelete,
            fields,
            actions,
            Inertia,
            handleConfirmDelete,
            form,
            reset,
            TeamPreview,
            baseRoute,
            clearFilters,
            clearSearch,
            filtersActive,
            multiselectClasses: getDefaultMultiselectTWClasses(),
            param,
            team_query,
            getTeams,
        };
    },
});
</script>
