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
            <label
                for="location"
                class="label label-text py-1 text-xs text-gray-400"
            >
                Locations:
            </label>
            <multiselect
                v-model="form.grlocation"
                class="py-2"
                id="location"
                mode="tags"
                :close-on-select="false"
                :create-option="true"
                :options="
                         this.$page.props.grlocations.map((loc) => ({
                            label: loc.name,
                            value: loc.gymrevenue_id,
                        }))
                    "
                :classes="multiselectClasses"
            />
        </div>

        <div class="form-control">
            <label
                for="lead_source"
                class="label label-text py-1 text-xs text-gray-400"
            >
                Source:
            </label>
            <multiselect
                v-model="form.leadsource"
                class="py-2"
                id="lead_source"
                mode="tags"
                :close-on-select="false"
                :create-option="true"
                :options="
                         this.$page.props.leadsources.map((leadsource) => ({
                            label: leadsource.name,
                            value: leadsource.id,
                        }))
                    "
                :classes="multiselectClasses"
            />
        </div>

        <div class="form-control">
            <label
                for="opportunity"
                class="label label-text py-1 text-xs text-gray-400"
            >
                Opportunity:
            </label>
            <multiselect
                v-model="form.opportunity"
                class="py-2"
                id="opportunity"
                mode="tags"
                :close-on-select="false"
                :create-option="true"
                :options="
                         this.$page.props.opportunities.map((opportunity) => ({
                            label: opportunity.value,
                            value: opportunity.value,
                        }))
                    "
                :classes="multiselectClasses"
            />
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
import Multiselect from "@vueform/multiselect";
import {getDefaultMultiselectTWClasses} from "@/utils";

export default defineComponent({
    components: {
        BeefySearchFilter,
        Multiselect
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
        return { form, reset, clearFilters, clearSearch,
            multiselectClasses: getDefaultMultiselectTWClasses() };
    },
});
</script>
