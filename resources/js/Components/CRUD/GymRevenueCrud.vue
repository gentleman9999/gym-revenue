<template>
    <jet-bar-container class="relative">
        <div class="flex flex-row items-center flex-wrap mb-4 gap-2">
            <slot name="filter">
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
                    <div v-if="this.$page.component ==='Leads/Index'">
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
            </slot>
            <div class="flex-grow" />
            <slot name="top-actions">
                <button
                    v-for="action in Object.values(topActions)"
                    class="btn"
                    :class="action.class"
                    @click.prevent="() => action.handler({ data, baseRoute })"
                >
                    {{ action.label }}
                </button>
            </slot>
        </div>

        <template v-if="tableComponent && cardsComponent">
            <div class="hidden lg:block">
                <component :is="tableComponent" v-bind="$props" />
            </div>
            <div class="lg:hidden">
                <component :is="cardsComponent" v-bind="$props" />
            </div>
        </template>
        <template v-else>
            <component :is="tableComponent || cardsComponent" v-bind="$props" />
        </template>

        <slot name="pagination">
            <pagination class="mt-4" :links="resource.links" />
        </slot>
    </jet-bar-container>
    <preview-modal v-if="previewComponent" :preview-component="previewComponent" :model-name="modelName"/>
</template>

<script>
import { ref, defineComponent, watch } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { merge } from "lodash";
import Pagination from "@/Components/Pagination";
import GymRevenueDataCards from "./GymRevenueDataCards";
import GymRevenueDataTable from "./GymRevenueDataTable";
import SearchFilter from "@/Components/CRUD/SearchFilter";
import JetBarContainer from "@/Components/JetBarContainer";
import PreviewModal from "@/Components/CRUD/PreviewModal";
import LeadForm from '@/Pages/Leads/Partials/LeadForm'
import {useSearchFilter} from "./helpers/useSearchFilter";

export default defineComponent({
    components: {
        GymRevenueDataCards,
        GymRevenueDataTable,
        Pagination,
        SearchFilter,
        JetBarContainer,
        LeadForm,
        PreviewModal
    },
    props: {
        fields: {
            type: Array,
        },
        resource: {
            type: Object,
        },
        baseRoute: {
            type: String,
            // required: true,
        },
        modelName: {
            type: String,
            default: "record",
        },
        modelNamePlural: {
            type: String,
        },
        titleField: {
            type: String,
        },
        cardComponent: {
            type: Object,
        },
        rowComponent: {
            type: Object,
        },
        actions: {
            type: [Object, Boolean],
            default: {},
        },
        topActions: {
            type: [Object, Boolean],
            default: {},
        },
        tableComponent: {
            type: Object,
            default: GymRevenueDataTable,
        },
        cardsComponent: {
            type: Object,
            default: GymRevenueDataCards,
        },
        previewComponent: {
            type: Object
        }
    },

    setup(props) {
        const {form, reset} = useSearchFilter(props.baseRoute);

        const defaultTopActions = {
            create: {
                label: `Create ${props.modelName}`,
                // handler: () =>
                //     Inertia.visit(route(`${props.baseRoute}.create`)),
                handler: () => {
                    console.log('handler',`${props.baseRoute}.create`, route(`${props.baseRoute}.create`) )
                    Inertia.visit(route(`${props.baseRoute}.create`));
                },
                class: ["btn-primary"],
            },
        };
        let topActions = [];
        if (props.topActions) {
            topActions = Object.values(
                merge({ ...defaultTopActions }, props.topActions)
            )
                .filter((action) => action)
                .filter((action) =>
                    action?.shouldRender ? action.shouldRender(props) : true
                );
        }
        return { form, topActions, reset };
    },
},

);

</script>

