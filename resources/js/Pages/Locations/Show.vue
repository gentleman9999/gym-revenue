<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl  leading-tight">
                Locations
            </h2>
        </template>
        <jet-bar-container>
            <div class="flex flex-row items-center mb-4">
                <search-filter v-model:modelValue="form.search" class="w-full max-w-md mr-4" @reset="reset">
                    <div class="block py-2 text-xs ">Trashed:</div>
                    <select v-model="form.trashed" class="mt-1 w-full form-select">
                        <option :value="null"/>
                        <option value="with">With Trashed</option>
                        <option value="only">Only Trashed</option>
                    </select>
                </search-filter>
                <div class="flex-grow"/>
                <inertia-link
                    class="btn justify-self-end"
                    :href="route('locations.create')">
                    <span>Create Location</span>
                </inertia-link>
            </div>
            <gym-revenue-table :headers="tableHeaders">
                <tr v-for="location in locations.data" :key="location.id" class="hover"
                    @dblclick="!location?.deleted_at && $inertia.visit(route('locations.edit', location.id))">
                    <td v-if="!isClientUser">{{ location.client.name }}</td>
                    <td>{{ location.name }}</td>
                    <td>{{ location.city }}</td>
                    <td>{{ location.state }}</td>
                    <td>
                        <jet-bar-badge text="Active" type="success" v-if="location.active"/>
                        <jet-bar-badge text="Inactive" type="danger" v-else/>
                    </td>
                    <td class="flex flex-row justify-center space-x-2">
                        <inertia-link class=" hover:"
                              :href="route('locations.edit', location.id)" v-if="!location?.deleted_at">
                            <jet-bar-icon type="pencil" fill/>

                        </inertia-link>
                        <!--                        <inertia-link :href="route('locations.delete', location.id)" class=" hover:">-->
                        <!--@todo: We need to add a confirmation before deleting to avoid accidental deletes-->
                        <button @click=" location?.deleted_at ? $inertia.post(route('locations.restore', location.id)) : $inertia.delete(route('locations.delete', location.id))"
                                class=" hover:">
                            <jet-bar-icon :type="location?.deleted_at ? 'untrash' : 'trash'" fill/>
                        </button>
                        <!--                        </inertia-link>-->
                    </td>
                </tr>

                <tr v-if="!locations?.data?.length">
                    <td colspan="6">No Locations found.</td>
                </tr>
                <template #pagination>
                    <pagination  class="mt-6" :links="locations.links"/>
                </template>

            </gym-revenue-table>

        </jet-bar-container>
    </app-layout>
</template>

<script>
import {defineComponent} from 'vue'
import AppLayout from '@/Layouts/AppLayout'
import JetSectionBorder from '@/Jetstream/SectionBorder'
import Button from '@/Components/Button'
import JetBarContainer from "@/Components/JetBarContainer";
import JetBarAlert from "@/Components/JetBarAlert";
import JetBarStatsContainer from "@/Components/JetBarStatsContainer";
import JetBarStatCard from "@/Components/JetBarStatCard";
import JetBarBadge from "@/Components/JetBarBadge";
import JetBarIcon from "@/Components/JetBarIcon";
import Pagination from "@/Components/Pagination";
import SearchFilter from "@/Components/SearchFilter";
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import GymRevenueTable from "@/Components/GymRevenueTable";


export default defineComponent({
    components: {
        AppLayout,
        JetSectionBorder,
        Button,
        JetBarContainer,
        JetBarAlert,
        JetBarStatsContainer,
        JetBarStatCard,
        JetBarBadge,
        JetBarIcon,
        Pagination,
        SearchFilter,
        GymRevenueTable
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
