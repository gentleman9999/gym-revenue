<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl  leading-tight">
                File Manager
            </h2>
        </template>
        <jet-bar-container class="relative">
            <div class="flex flex-row items-center mb-4">
<!--                <search-filter v-model:modelValue="form.search" class="w-full max-w-md mr-4" @reset="reset">-->
<!--                    <div class="block py-2 text-xs text-gray-400">Trashed:</div>-->
<!--                    <select v-model="form.trashed" class="mt-1 w-full form-select">-->
<!--                        <option :value="null"/>-->
<!--                        <option value="with">With Trashed</option>-->
<!--                        <option value="only">Only Trashed</option>-->
<!--                    </select>-->
<!--                </search-filter>-->
                <div class="flex-grow"/>
                <inertia-link
                    class="btn justify-self-end"
                    :href="route('files.upload')">
                    <span>Upload</span>
                </inertia-link>
            </div>
            <gym-revenue-table :headers="tableHeaders">
                <tr class="hover" v-for="file in files?.data" :key="file.id"
                    @dblclick="!file?.deleted_at && $inertia.visit(route('locations.edit', file.id))">
<!--                    <td v-if="!isClientUser">{{ file.client.name }}</td>-->
                    <td>{{ file.filename }}</td>
                    <td>{{ new Date(file.created_at).toLocaleString() }}</td>
                    <td>{{ prettyBytes(file.size) }}</td>
                    <td class="flex flex-row justify-center space-x-2">
<!--                        <inertia-link class="text-gray-400 hover:text-gray-500"-->
<!--                              :href="route('locations.edit', file.id)" v-if="!file?.deleted_at">-->
<!--                            <jet-bar-icon type="pencil" fill/>-->

<!--                        </inertia-link>-->
                        <!--                        <inertia-link :href="route('locations.delete', location.id)" class="text-gray-400 hover:text-gray-500">-->
                        <!--@todo: We need to add a confirmation before deleting to avoid accidental deletes-->
                        <button @click=" file?.deleted_at ? $inertia.post(route('files.restore', file.id)) : $inertia.delete(route('files.trash', file.id))">
                            <jet-bar-icon :type="file?.deleted_at ? 'untrash' : 'trash'" fill/>
                        </button>
                        <!--                        </inertia-link>-->
                    </td>
                </tr>

                <tr v-if="!files?.data?.length">
                    <td colspan="6">No Files found.</td>
                </tr>

            </gym-revenue-table>

<!--            <pagination class="mt-6" :inertia-links="locations.inertia-links"/>-->
        </jet-bar-container>
    </app-layout>
</template>

<script>
import {defineComponent} from 'vue'
import prettyBytes from "pretty-bytes";
import AppLayout from '@/Layouts/AppLayout.vue'
import JetSectionBorder from '@/Jetstream/SectionBorder.vue'
import Button from '@/Components/Button.vue'
import JetBarContainer from "@/Components/JetBarContainer";
import JetBarAlert from "@/Components/JetBarAlert";
import JetBarStatsContainer from "@/Components/JetBarStatsContainer";
import JetBarStatCard from "@/Components/JetBarStatCard";
import GymRevenueTable from "@/Components/GymRevenueTable";
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
        Button,
        JetBarContainer,
        JetBarAlert,
        JetBarStatsContainer,
        JetBarStatCard,
        GymRevenueTable,
        JetBarBadge,
        JetBarIcon,
        Pagination,
        SearchFilter
    },
    props: ['sessions', 'files', 'title', 'isClientUser', 'filters'],
    setup(){
        return {prettyBytes}
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get(this.route('files'), pickBy(this.form), {
                    preserveState: true,
                    preserveScroll: true
                })
            }, 150)
        }
    },
    data() {
        return {
            form: {
                // search: this.filters.search,
                // trashed: this.filters.trashed,
            },
        }
    },

    computed: {
        tableHeaders() {
            // if (this.isClientUser) {
            //     return ['name', 'city', 'state', 'active', ''];
            // }

            return [ 'filename', 'created_at', 'size', '']
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
