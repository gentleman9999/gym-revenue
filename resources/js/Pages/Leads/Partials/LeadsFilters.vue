<template>

    <search-filter
        v-model:modelValue="form.search"
        class="w-full max-w-md mr-4"
        @reset="reset"
    >
        <div class="block py-2 text-xs text-gray-400">Trashed:</div>
        <select
            v-model="form.trashed"
            class="mt-1 w-full form-select"
        >
            <option :value="null" />
            <option value="with">With Trashed</option>
            <option value="only">Only Trashed</option>
        </select>
        <!-- filters if within Leads/Index -->
        <div>
            <!--
             We will need a calendar function to pick date to and from to replace this
             -->
            <!-- calendar is needed but more important the leads need to have set different created_at dates
                      <div class="block py-2 text-xs text-gray-400">Created:</div>
                      <select
                          v-model="form.createdat"
                          class="mt-1 w-full form-select"
                      >
                          <option :value="null" />
                          <option value="2022-01-10">2022-01-10</option>
                          <option value="2022-01-12">2022-01-12</option>
                      </select>
            -->
            <div class="block py-2 text-xs text-gray-400">Type:</div>
            <select
                v-model="form.typeoflead"
                class="mt-1 w-full form-select"
            >
                <option :value="null" />
                <option v-for="(lead_types, i) in this.$page.props.lead_types" :value="lead_types.id">{{lead_types.name }}
                </option>
            </select>
            <div  class="block py-2 text-xs text-gray-400">Location:</div>
            <select
                v-model="form.grlocation"
                class="mt-1 w-full form-select"
            >
                <option :value="null" />
                <option v-for="(grlocations, i) in this.$page.props.grlocations" :value="grlocations.gymrevenue_id">{{grlocations.name }}
                </option>
            </select>
            <div class="block py-2 text-xs text-gray-400">Source:</div>
            <select
                v-model="form.leadsource"
                class="mt-1 w-full form-select"
            >
                <option :value="null" />
                <option v-for="(leadsources, i) in this.$page.props.leadsources" :value="leadsources.id">{{leadsources.name }}
                </option>
            </select>


            <!--
        Claimed or unclaimed


                <div class="block py-2 text-xs text-gray-400">Claimed</div>
<button  href="route('data.leads.claimed')">Claimed</button>


leadclaimed
----This is a section for the claimed or unclaimed just not sure what that is yet -----
            <div class="block py-2 text-xs text-gray-400">Claimed/Unclaimed:</div>
            <select
                v-model="form.leadsclaimed"
                class="mt-1 w-full form-select"
            >
                <option :value="null" />
                <option value="leadsclaimed">Claimed</option>
                <option value="unclaimed">UnClaimed</option>

            </select>
                -->
            <!--

By Claimed employee
----This is a section for the claimed by employee  just not sure what that is yet -------
<div class="block py-2 text-xs text-gray-400">Source:</div>
<select
v-model="form.leadsource"
class="mt-1 w-full form-select"
>
<option :value="null" />
<option value="40">source-4</option>
<option value="41">source-5</option>
<option value="42">source-6</option>
</select>
-->
        </div>
        <!-- End filters if within Leads/Index -->
    </search-filter>
</template>

<script>
import { defineComponent } from "vue";
import SearchFilter from "@/Components/CRUD/SearchFilter";
import {useSearchFilter} from "@/Components/CRUD/helpers/useSearchFilter";

export default defineComponent({
    components: {
        SearchFilter,
    },
    props:{
        baseRoute:{
            type: String,
            required: true
        }
    },
    setup(props) {
        const { form, reset } = useSearchFilter(props.baseRoute);
        return { form, reset };
    },
});
</script>
