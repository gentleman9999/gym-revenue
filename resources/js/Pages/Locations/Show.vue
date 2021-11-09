<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Locations
            </h2>
        </template>
        <jet-bar-container>
            <div class="flex flex-row items-center mb-4">
                <search-filter v-model:modelValue="form.search" class="w-full max-w-md mr-4" @reset="reset">
                    <div class="block py-2 text-xs text-gray-400">Trashed:</div>
                    <select v-model="form.trashed" class="mt-1 w-full form-select">
                        <option :value="null"/>
                        <option value="with">With Trashed</option>
                        <option value="only">Only Trashed</option>
                    </select>
                </search-filter>
                <div class="flex-grow"/>
                <Link
                    class="btn justify-self-end"
                    :href="route('locations.create')">
                    <span>Create Location</span>
                </Link>
            </div>
            <jet-bar-table :headers="tableHeaders">
                <tr class="hover:bg-gray-50" v-for="location in locations.data" :key="location.id"
                    @dblclick="$inertia.visit(route('locations.edit', location.id))">
                    <jet-bar-table-data v-if="!isClientUser">{{ location.client.name }}</jet-bar-table-data>
                    <jet-bar-table-data>{{ location.name }}</jet-bar-table-data>
                    <jet-bar-table-data>{{ location.city }}</jet-bar-table-data>
                    <jet-bar-table-data>{{ location.state }}</jet-bar-table-data>
                    <jet-bar-table-data>
                        <jet-bar-badge text="Active" type="success" v-if="location.active"/>
                        <jet-bar-badge text="Inactive" type="danger" v-else/>
                    </jet-bar-table-data>
                    <jet-bar-table-data class="flex flex-row justify-center space-x-2">
                        <Link class="text-gray-400 hover:text-gray-500"
                              :href="route('locations.edit', location.id)">
                            <jet-bar-icon type="pencil" fill/>

                        </Link>
                        <!--                        <Link :href="route('locations.delete', location.id)" class="text-gray-400 hover:text-gray-500">-->
                        <!--@todo: We need to add a confirmation before deleting to avoid accidental deletes-->
                        <button @click="$inertia.delete(route('locations.delete', location.id))"
                                class="text-gray-400 hover:text-gray-500">
                            <jet-bar-icon type="trash" fill/>
                        </button>
                        <!--                        </Link>-->
                    </jet-bar-table-data>
                </tr>

                <tr class="hover:bg-gray-50" v-if="!locations?.data?.length">
                    <jet-bar-table-data colspan="5">No Locations found.</jet-bar-table-data>
                </tr>

            </jet-bar-table>

            <pagination class="mt-6" :links="locations.links"/>

        </jet-bar-container>


    </app-layout>
</template>

<script>
import {defineComponent} from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import JetSectionBorder from '@/Jetstream/SectionBorder.vue'
import Button from '@/Jetstream/Button.vue'
import {Link} from '@inertiajs/inertia-vue3';
import JetBarContainer from "@/Components/JetBarContainer";
import JetBarAlert from "@/Components/JetBarAlert";
import JetBarStatsContainer from "@/Components/JetBarStatsContainer";
import JetBarStatCard from "@/Components/JetBarStatCard";
import JetBarTable from "@/Components/JetBarTable";
import JetBarTableData from "@/Components/JetBarTableData";
import JetBarBadge from "@/Components/JetBarBadge";
import JetBarIcon from "@/Components/JetBarIcon";
import Pagination from "@/Components/Pagination";
import SearchFilter from "@/Components/SearchFilter";
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'


export default defineComponent({
    components: {
        AppLayout,
        JetSectionBorder,
        Link,
        Button,
        JetBarContainer,
        JetBarAlert,
        JetBarStatsContainer,
        JetBarStatCard,
        JetBarTable,
        JetBarTableData,
        JetBarBadge,
        JetBarIcon,
        Pagination,
        SearchFilter
    },
    props: ['sessions', 'locations', 'title', 'isClientUser', 'filters'],
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get(this.route('locations'), pickBy(this.form), {
                    preserveState: true,
                    preserveScroll: true
                })
            }, 150)
        }
    },
    data() {
        return {
            form: {
                search: this.filters.search,
                trashed: this.filters.trashed,
            },
        }
    },

    computed: {
        tableHeaders() {
            if (this.isClientUser) {
                return ['name', 'city', 'state', 'active', ''];
            }

            return ['client', 'name', 'city', 'state', 'active', '']
        }
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
    },
    mounted() {
    }
})
</script>
