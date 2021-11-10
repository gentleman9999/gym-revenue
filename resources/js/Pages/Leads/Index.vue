<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Leads
            </h2>
        </template>
        <jet-bar-container>
            <div class="flex flex-row items-center mb-4">
                <div class="flex-grow"/>
                <Link
                    class="btn justify-self-end"
                    :href="route('data.leads.create')">
                    <span>Create Lead</span>
                </Link>
            </div>
            <jet-bar-table :headers="tableHeaders">
                <tr class="hover:bg-gray-50" v-if="leads.length === 0">
                    <jet-bar-table-data></jet-bar-table-data>
                    <jet-bar-table-data></jet-bar-table-data>
                    <jet-bar-table-data>No Data Available</jet-bar-table-data>
                    <jet-bar-table-data></jet-bar-table-data>
                    <jet-bar-table-data></jet-bar-table-data>
                    <jet-bar-table-data></jet-bar-table-data>
                </tr>
            </jet-bar-table>
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
    props: ['leads', 'title', 'isClientUser', 'filters'],
    computed: {
        tableHeaders() {
            /*
            if (this.isClientUser) {
                return ['name', 'city', 'state', 'active', ''];
            }
             */

            return ['date', 'first_name', 'last_name', 'location', 'lead_type', ''];
        }
    },
});
</script>

<style scoped>

</style>
