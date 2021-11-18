<template>
    <app-layout :title="title">
        <template #header>
            <div class="text-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Mass Communication Dashboard
                </h2>
                <small>Send SMS and Emails in Bulk here!</small>
            </div>
        </template>

        <jet-bar-container>
            <div class="flex flex-col pb-2">
                <div class="top-drop-row stop-drop-roll flex flex-row justify-center mb-4 xl-justify-right">
                    <div class="relative">
                        <jet-dropdown align="right" v-if="true">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                        Audiences

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <template v-if="true">
                                        <!-- Location Switcher -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Select an Audience
                                        </div>

                                        <div class="h-40 lg:h-20 overflow-y-scroll">
                                            <template v-for="(lbl, slug) in audiences" :key="slug">
                                                <form @submit.prevent="comingSoon(location)">
                                                    <jet-dropdown-link as="button">
                                                        <div class="flex items-center">
                                                            <svg v-if="activeAudience === slug" class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            <div>{{ lbl }}</div>
                                                        </div>
                                                    </jet-dropdown-link>
                                                </form>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </jet-dropdown>
                    </div>

                </div>
                <div class="top-navigation flex flex-col xl:flex-row xl-justify-between">
                    <div class="flex flex-wrap xl:flex-row justify-center xl-justify-left">
                        <div class="mr-1">
                            <Link
                                class="btn justify-self-end"
                                :href="route('data.leads.create')">
                                <span>Emails Templates <span class="bg-info p-1">0</span> <span class=" p-1 bg-success">0</span></span>
                            </Link>
                        </div>
                        <div class="mr-1 ">
                            <Link
                                class="btn justify-self-end"
                                :href="route('data.leads.create')">
                                <span>SMS Templates <span class="bg-info p-1">0</span> <span class=" p-1 bg-success">0</span></span>
                            </Link>
                        </div>
                        <div class="mr-1 mt-1 sm:mt-0">
                            <Link
                                class="btn justify-self-end"
                                :href="route('data.leads.create')">
                                <span>Email Campaigns <span class="bg-info p-1">0</span> <span class=" p-1 bg-success">0</span></span>
                            </Link>
                        </div>
                        <div class="mt-1 md:mt-0">
                            <Link
                                class="btn justify-self-end"
                                :href="route('data.leads.create')">
                                <span>SMS Campaigns <span class="bg-info p-1">0</span> <span class=" p-1 bg-success">0</span></span>
                            </Link>
                        </div>
                    </div>

                    <div class="flex flex-row justify-center xl-justify-right">
                        <div class="mt-2 mr-1 xl:mt-0">
                            <Link
                                class="btn justify-self-end"
                                :href="route('data.leads.create')">
                                <span>+ New Email</span>
                            </Link>
                        </div>
                        <div class="mt-2 ml-1 xl:mt-0">
                            <Link
                                class="btn justify-self-end"
                                :href="route('data.leads.create')">
                                <span>+ New SMS</span>
                            </Link>
                        </div>
                    </div>
                </div>
                <div class="comms-content"></div>
                </div>
            <div class="border-b-4 border-blue-300"></div>
        </jet-bar-container>
    </app-layout>
</template>

<script>
import {defineComponent} from 'vue'
import {Link} from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue'
import JetDropdown from '@/Jetstream/Dropdown'
import JetDropdownLink from '@/Jetstream/DropdownLink'
import JetBarContainer from "@/Components/JetBarContainer";


export default defineComponent({
    name: "MassCommsDashboard",
    components: {
        Link,
        AppLayout,
        JetDropdown,
        JetDropdownLink,
        JetBarContainer
    },
    props: ['title', 'audiences', 'activeAudience'],
    setup(props) {},
    watch: {},
    data() {
        return {};
    },
    computed: {

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

<style scoped>


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
