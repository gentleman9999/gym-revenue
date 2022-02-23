<template>
    <beefy-search-filter
        v-model:modelValue="form.search"
        class="w-full max-w-md mr-4"
        @reset="reset"
        @clear-filters="clearFilters"
        @clear-search="clearSearch"
    >
        <div class="form-control">
            <label for="trashed"> Trashed: </label>
            <select
                id="trashed"
                v-model="form.trashed"
                class="mt-1 w-full form-select"
            >
                <option :value="null" />
                <option value="with">With Trashed</option>
                <option value="only">Only Trashed</option>
            </select>
        </div>
        <div class="form-control">
            <label for="lead_type"> Type: </label>
            <select
                id="lead_type"
                v-model="form.typeoflead"
                class="mt-1 w-full form-select"
            >
                <option :value="null" />
                <option
                    v-for="(lead_types, i) in this.$page.props.lead_types"
                    :value="lead_types.id"
                >
                    {{ lead_types.name }}
                </option>
            </select>
        </div>
        <div class="form-control">
            <label for="location" class="block py-1 text-xs text-gray-400">
                Location:
            </label>
            <select
                id="location"
                v-model="form.grlocation"
                class="mt-1 w-full form-select"
            >
                <option :value="null" />
                <option
                    v-for="(grlocations, i) in this.$page.props.grlocations"
                    :value="grlocations.gymrevenue_id"
                >
                    {{ grlocations.name }}
                </option>
            </select>
        </div>
        <div class="form-control">
            <label
                for="lead_source"
                class="label label-text py-1 text-xs text-gray-400"
            >
                Source:
            </label>
            <select
                id="lead_source"
                v-model="form.leadsource"
                class="mt-1 w-full form-select"
            >
                <option :value="null" />
                <option
                    v-for="(leadsources, i) in this.$page.props.leadsources"
                    :value="leadsources.id"
                >
                    {{ leadsources.name }}
                </option>
            </select>
        </div>
    </beefy-search-filter>
</template>

<style scoped>
label {
    @apply label label-text py-0 text-xs text-gray-400;
}
input {
    @apply input input-sm input-bordered;
}
select {
    @apply select select-sm select-bordered;
}
button{
    @apply btn btn-sm;
}
</style>

<script>
import { defineComponent } from "vue";
import { useSearchFilter } from "@/Components/CRUD/helpers/useSearchFilter";
import BeefySearchFilter from "@/Components/CRUD/BeefySearchFilter";

export default defineComponent({
    components: {
        BeefySearchFilter,
    },
    props: {
        baseRoute: {
            type: String,
            required: true,
        },
    },
    setup(props) {
        const { form, reset, clearFilters, clearSearch } = useSearchFilter(
            props.baseRoute
        );
        return { form, reset, clearFilters, clearSearch };
    },
});
</script>
