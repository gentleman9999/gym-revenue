<template>
    <app-layout title="Profile">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Locations
            </h2>
        </template>
        <jet-bar-container>
            <jet-bar-table :headers="['name', 'city', 'state','active', '']">
                <tr class="hover:bg-gray-50" v-for="location in locations.data" :key="location.id" @dblclick="$inertia.visit(route('locations.edit', location.id))">
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
                            <button @click="$inertia.delete(route('locations.delete', location.id))" class="text-gray-400 hover:text-gray-500">
                                <jet-bar-icon type="trash" fill/>
                            </button>
<!--                        </Link>-->
                    </jet-bar-table-data>
                </tr>

                <tr class="hover:bg-gray-50" v-if="!locations?.data?.length">
                    <jet-bar-table-data colspan="5">No Locations found.</jet-bar-table-data>
                </tr>

            </jet-bar-table>
            <Link
                class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                :href="route('locations.create')">
                <span>Create Location</span>
            </Link>
            <pagination class="mt-6" :links="locations.links"/>

        </jet-bar-container>
        <!--            <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">-->
        <!--                <label class="block text-gray-700">Trashed:</label>-->
        <!--                <select v-model="form.trashed" class="mt-1 w-full form-select">-->
        <!--                    <option :value="null"/>-->
        <!--                    <option value="with">With Trashed</option>-->
        <!--                    <option value="only">Only Trashed</option>-->
        <!--                </select>-->
        <!--            </search-filter>-->


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


export default defineComponent({
    props: ['sessions', 'locations'],

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
        Pagination
    },
})
</script>
