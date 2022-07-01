<template>
    <beefy-search-filter
        v-model:modelValue="form.search"
        class="w-full max-w-md mr-4"
        :filtersActive="filtersActive"
        @reset="reset"
        @clear-filters="clearFilters"
        @clear-search="clearSearch"
    >
        <div class="form-control">
            <label for="nameSearch" class="py-1 text-xs"> Name Search: </label>
            <input
                id="nameSearch"
                v-model="form.nameSearch"
                placeholder="John"
            />
        </div>

        <div class="form-control">
            <label for="phoneSearch" class="py-1 text-xs">
                Phone Search:
            </label>
            <input
                id="phoneSearch"
                v-model="form.phoneSearch"
                placeholder="5558675309"
            />
        </div>

        <div class="form-control">
            <label for="emailSearch" class="py-1 text-xs">
                Email Search:
            </label>
            <input
                id="emailSearch"
                v-model="form.emailSearch"
                placeholder="noreply@notarealaddress.com"
            />
        </div>

        <div class="form-control">
            <label for="agreementSearch" class="py-1 text-xs">
                Agreement Number Search:
            </label>
            <input
                id="agreementSearch"
                v-model="form.agreementSearch"
                placeholder="1545804477"
            />
        </div>

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
            <label for="last_updated"> Sort By: </label>
            <select
                id="last_updated"
                v-model="form.lastupdated"
                class="mt-1 w-full form-select"
            >
                <option :value="null" />
                <option value="ASC">Most Recent</option>
                <option value="DESC">Least Recent</option>
            </select>
        </div>

        <div class="form-control">
            <label for="leads_claimed" class="py-1 text-xs">
                Leads Claimed By:
            </label>
            <multiselect
                v-model="form.leadsclaimed"
                class="py-2"
                id="leads_claimed"
                mode="tags"
                :close-on-select="false"
                :create-option="true"
                :options="
                    this.$page.props.leadsclaimed.map((user) => ({
                        label: user.name,
                        value: user.id,
                    }))
                "
                :classes="multiselectClasses"
            />
        </div>

        <div class="form-control">
            <label for="lead_type" class="py-1 text-xs"> Lead Type: </label>
            <multiselect
                v-model="form.typeoflead"
                class="py-2"
                id="lead_type"
                mode="tags"
                :close-on-select="false"
                :create-option="true"
                :options="
                    this.$page.props.lead_types.map((type) => ({
                        label: type.name,
                        value: type.id,
                    }))
                "
                :classes="multiselectClasses"
            />
        </div>

        <div class="form-control">
            <label for="location" class="py-1 text-xs"> Locations: </label>
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
            <label for="lead_source" class="py-1 text-xs"> Source: </label>
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
            <label for="opportunity" class="py-1 text-xs"> Opportunity: </label>
            <multiselect
                v-model="form.opportunity"
                class="py-2"
                id="opportunity"
                mode="tags"
                :close-on-select="false"
                :create-option="true"
                :options="
                    this.$page.props.opportunities.map((opportunity) => ({
                        label: opportunity,
                        value: opportunity,
                    }))
                "
                :classes="multiselectClasses"
            />
        </div>

        <div class="form-control">
            <label for="date_of_birth" class="py-1 text-xs">
                Date of Birth:
            </label>
            <DatePicker
                id="date_of_birth"
                v-model="form.date_of_birth"
                :enableTimePicker="false"
                range
                dark
            />
        </div>
    </beefy-search-filter>
</template>

<style scoped>
label {
    @apply py-0 text-xs;
}
input {
    @apply input input-sm input-bordered;
}
select {
    @apply select select-sm select-bordered;
}
button {
    @apply btn btn-sm;
}
</style>

<script>
import { defineComponent } from "vue";
import { useSearchFilter } from "@/Components/CRUD/helpers/useSearchFilter";
import BeefySearchFilter from "@/Components/CRUD/BeefySearchFilter.vue";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses } from "@/utils";
import DatePicker from "@vuepic/vue-datepicker";

export default defineComponent({
    components: {
        BeefySearchFilter,
        Multiselect,
        DatePicker,
    },
    props: {
        baseRoute: {
            type: String,
            required: true,
        },
    },
    setup(props) {
        const { form, reset, clearFilters, clearSearch, filtersActive } =
            useSearchFilter(props.baseRoute);
        return {
            form,
            filtersActive,
            reset,
            clearFilters,
            clearSearch,
            multiselectClasses: getDefaultMultiselectTWClasses(),
        };
    },
});
</script>
