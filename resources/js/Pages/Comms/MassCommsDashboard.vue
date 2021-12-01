<template>
    <app-layout :title="title">
        <template #header>
            <div class="text-center">
                <h2 class="font-semibold text-xl  leading-tight">
                    Mass Communication Dashboard
                </h2>
                <small>Send SMS and Emails in Bulk here!</small>
            </div>
        </template>

        <jet-bar-container>
            <div class="flex flex-col pb-2">
                <div class="top-drop-row stop-drop-roll flex flex-row justify-center mb-4 xl:justify-end">
                    <jet-dropdown align="end" v-if="true">
                        <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                            class="inline-flex items-center px-3 py-2 border border-white text-sm leading-4 font-medium rounded-md  bg-white hover:bg-base-100 bg-base-200 focus:outline-none focus:bg-base-100 active:bg-base-100 transition">
                                        {{
                                            (activeAudience in audiences) ? 'Audience: ' + audiences[activeAudience] : 'Audiences'
                                        }}

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                  d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </span>
                        </template>

                        <template #content>
                            <!-- Team Management -->
                            <template v-if="true">
                                <!-- Location Switcher -->
                                <div class="block px-4 py-2 text-xs ">
                                    Select an Audience(s)
                                </div>

                                <ul class="menu compact">
                                    <li v-for="(lbl, slug) in audiences" :key="slug">
                                        <inertia-link href="#" @click="comingSoon">
                                            <svg v-if="activeAudience === slug" class="mr-2 h-5 w-5 text-green-400"
                                                 fill="none" stroke-linecap="round" stroke-linejoin="round"
                                                 stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ lbl }}
                                        </inertia-link>
                                    </li>
                                </ul>
                            </template>
                        </template>
                    </jet-dropdown>

                </div>
                <div class="top-navigation flex flex-col xl:flex-row xl:justify-between">
                    <div class="flex flex-wrap xl:flex-row justify-center xl:justify-start">
                        <div class="mr-1">
                            <inertia-link
                                class="btn justify-self-end"
                                :href="route('comms.email-templates')">
                                <span>Email Templates <span class="bg-info p-1">{{
                                        stats['email_templates'].created
                                    }}</span> <span class=" p-1 bg-success">{{ stats['email_templates'].active }}</span></span>
                            </inertia-link>
                        </div>
                        <div class="mr-1 ">
                            <inertia-link
                                class="btn justify-self-end"
                                :href="route('comms.sms-templates')">
                                <span>SMS Templates <span class="bg-info p-1">{{
                                        stats['sms_templates'].created
                                    }}</span> <span class=" p-1 bg-success">{{
                                        stats['sms_templates'].active
                                    }}</span></span>
                            </inertia-link>
                        </div>
                        <div class="mr-1 mt-1 sm:mt-0">
                            <inertia-link
                                class="btn justify-self-end"
                                :href="route('comms.email-campaigns')">
                                <span>Email Campaigns <span class="bg-info p-1">{{
                                        stats['email_campaigns'].created
                                    }}</span> <span class=" p-1 bg-success">{{ stats['email_campaigns'].active }}</span></span>
                            </inertia-link>
                        </div>
                        <div class="mt-1 md:mt-0">
                            <inertia-link
                                class="btn justify-self-end"
                                :href="route('comms.sms-campaigns')">
                                <span>SMS Campaigns <span class="bg-info p-1">{{
                                        stats['sms_campaigns'].created
                                    }}</span> <span class=" p-1 bg-success">{{
                                        stats['sms_campaigns'].active
                                    }}</span></span>
                            </inertia-link>
                        </div>
                    </div>

                    <div class="flex flex-row justify-center xl:justify-end">
                        <div class="mt-2 mr-1 xl:mt-0">
                            <inertia-link
                                class="btn justify-self-end"
                                href="#" @click="comingSoon()">
                                <span>+ New Email</span>
                            </inertia-link>
                        </div>
                        <div class="mt-2 ml-1 xl:mt-0">
                            <inertia-link
                                class="btn justify-self-end"
                                href="#" @click="comingSoon()">
                                <span>+ New SMS</span>
                            </inertia-link>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-b-4 border-blue-300"></div>
            <!-- Page Content -->
            <div class="comms-content mt-4 flex flex-col lg:flex-row lg:w-full">
                <div class="left-section flex flex-col xl:w-1/3 mb-3 lg:mb-0 lg:mr-3">
                    <div class="total-audience-breakdown border-2 border-gray-300">
                        <div class="flex flex-col">
                            <div class="border-b-2 border-gray-300">
                                <h2 class="px-2 bg-secondary"> Total Audience</h2>
                                <div class="text-center bg-secondary">
                                    <h1 class="text-2xl">{{ stats['total_audience'] }}</h1>
                                </div>
                            </div>

                            <h2 class=" px-2"> Total Audience Breakdown</h2>
                            <div v-for="(lbl, slug) in audiences">
                                <p class="m-2"><b>{{ lbl }}</b> :{{ stats['audience_breakdown'][slug] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="right-section flex flex-col xl:w-2/3 mt-3 lg:mt-0 lg:ml-3">
                    <div class="current-feed border-2 border-gray-300">
                        <div class="bg-primary border-b-2 border-gray-300 py-2">
                            <h1 class="ml-2 bg-primary">Your Feed</h1>
                        </div>
                        <gym-revenue-table :headers="tableHeaders">
                            <tr v-if="historyFeed.length === 0">
                                <td>No Data Available.</td>
                            </tr>
                            <tr class="hover" v-else v-for="(log, idx) in historyFeed" :key="idx">
                                <td>{{ log.type }}</td>
                                <td>{{ log.recordName }}</td>
                                <td>{{ log.date }}</td>
                                <td>{{ log.by }}</td>
                            </tr>
                        </gym-revenue-table>
                    </div>
                </div>
            </div>
        </jet-bar-container>
    </app-layout>
</template>

<script>
import {defineComponent} from 'vue'
import AppLayout from '@/Layouts/AppLayout'
import JetDropdown from '@/Components/Dropdown'
import JetBarContainer from "@/Components/JetBarContainer";
import GymRevenueTable from "@/Components/GymRevenueTable";


export default defineComponent({
    name: "MassCommsDashboard",
    components: {
        AppLayout,
        JetDropdown,
        GymRevenueTable,
        JetBarContainer
    },
    props: ['title', 'audiences', 'activeAudience', 'stats', 'historyFeed'],
    setup(props) {
    },
    watch: {},
    data() {
        return {};
    },
    computed: {
        tableHeaders() {
            if (this.historyFeed.length > 0) {
                return ['action', 'template', 'date', 'by']
            }

            return [];

        },
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
    },
    mounted() {

    }
});
</script>
