<template>
    <app-layout :title="title">
        <template #header>
            <div class="text-center">
                <h2 class="font-semibold text-xl  leading-tight">
                    Email Template Management
                </h2>
                <small></small>
            </div>
        </template>
        <jet-bar-container>
            <div class="flex flex-col pb-2">
                <div class="top-drop-row stop-drop-roll flex flex-row justify-center mb-4 xl-justify-left">
                    <Link
                        class="btn justify-self-end"
                        :href="route('comms.dashboard')">
                        <span><font-awesome-icon :icon="['far', 'chevron-double-left']" size="16"/> Back</span>
                    </Link>
                </div>
            </div>
            <div class="top-navigation flex flex-col xl:flex-row xl-justify-between">
                <div class="flex flex-wrap xl:flex-row justify-center xl-justify-left">
                    <div class="mr-1">
                        <search-filter v-model:modelValue="form.search" class="w-full max-w-md mr-4" @reset="reset">
                            <div class="block py-2 text-xs ">Trashed:</div>
                            <select v-model="form.trashed" class="mt-1 w-full form-select">
                                <option :value="null"/>
                                <option value="with">With Trashed</option>
                                <option value="only">Only Trashed</option>
                            </select>
                        </search-filter>
                    </div>
                </div>

                <div class="flex flex-row justify-center xl-justify-right">
                    <div class="mt-2 ml-1 xl:mt-0">
                        <Link
                            class="btn justify-self-end"
                            href="#" @click="comingSoon()">
                            <span>+ New Template</span>
                        </Link>
                    </div>
                </div>
            </div>
            <div class="inner-template-index-content mt-4">
                <div class="template-table border-2 border-base-300 rounded-t-md">
                    <div class="flex flex-col bg-secondary rounded-t-md">
                        <div class="border-b-2 border-gray-300 py-4">
                            <h2 class="px-4">Templates</h2>
                        </div>
                    </div>
                    <gym-revenue-table :headers="tableHeaders">
                        <tr class="hover:bg-base-100" v-if="templates.data.length === 0">
                            <jet-bar-table-data></jet-bar-table-data>
                            <jet-bar-table-data></jet-bar-table-data>
                            <jet-bar-table-data></jet-bar-table-data>
                            <jet-bar-table-data>No Data Available.</jet-bar-table-data>
                            <jet-bar-table-data></jet-bar-table-data>
                            <jet-bar-table-data></jet-bar-table-data>
                        </tr>
                    </gym-revenue-table>
                </div>

            </div>
        </jet-bar-container>
    </app-layout>
</template>

<script>
import {defineComponent} from "vue";
import {Link} from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue'
import JetDropdown from '@/Jetstream/Dropdown'
import JetDropdownLink from '@/Jetstream/DropdownLink'
import JetBarContainer from "@/Components/JetBarContainer";
import SearchFilter from "@/Components/SearchFilter";
import JetBarTableData from "@/Components/JetBarTableData";
import GymRevenueTable from "@/Components/GymRevenueTable";

import { library } from '@fortawesome/fontawesome-svg-core';
import { faChevronDoubleLeft } from '@fortawesome/pro-regular-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import mapValues from "lodash/mapValues";
library.add(faChevronDoubleLeft)

export default defineComponent({
    name: "EmailTemplatesIndex",
    components: {
        Link,
        AppLayout,
        JetDropdown,
        SearchFilter,
        FontAwesomeIcon,
        GymRevenueTable,
        JetBarContainer,
        JetBarTableData,
        JetDropdownLink
    },
    props: ['title', 'filters', 'templates'],
    setup(props) {},
    watch: {},
    data() {
        return {
            form: {
                search: this.filters.search,
                trashed: this.filters.trashed,
            },
        };
    },
    computed: {
        tableHeaders() {
            if(this.templates.data.length > 0) {
                return ['name', 'status', 'type', 'date updated', 'updated by', '']
            }

            return [];

        }
    },
    methods: {
        comingSoon() {
            new Noty({
                type: 'warning',
                theme: 'sunset',
                text: 'Feature Coming Soon!',
                timeout: 7500
            }).show();
        },
        reset() {
            this.form = mapValues(this.form, () => null)
        },
    },
    mounted() {}
});
</script>

<style scoped>
@media (min-width: 1024px) {
    .lg-w-full {
        width: 100%;
    }

    .lg-w-30 {
        width: 30%;
    }

    .lg-w-70 {
        width: 70%;
    }
}

@media (min-width: 1280px) {
    .xl-justify-left {
        justify-content: flex-start;
    }

    .xl-justify-right {
        justify-content: flex-end;
    }

    .xl-justify-between {
        justify-content: space-between;
    }
}
</style>
