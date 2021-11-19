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
                    <jet-dropdown align="right" v-if="true">
                        <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-white text-sm leading-4 font-medium rounded-md  bg-white hover:bg-base-100 bg-base-200 focus:outline-none focus:bg-base-100 active:bg-base-100 transition">
                                        {{ (activeAudience in audiences) ? 'Audience: ' +audiences[activeAudience] : 'Audiences'}}

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
                                    <div class="block px-4 py-2 text-xs ">
                                        Select an Audience(s)
                                    </div>

                                    <div class="h-40 lg:h-20 overflow-y-scroll">
                                        <template v-for="(lbl, slug) in audiences" :key="slug">
                                            <form @submit.prevent="comingSoon()">
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
                <div class="top-navigation flex flex-col xl:flex-row xl:justify-between">
                    <div class="flex flex-wrap xl:flex-row justify-center xl:justify-start">
                        <div class="mr-1">
                            <Link
                                class="btn justify-self-end"
                                :href="route('comms.email-templates')">
                                <span>Email Templates <span class="bg-info p-1">{{ stats['email_templates'].created }}</span> <span class=" p-1 bg-success">{{ stats['email_templates'].active }}</span></span>
                            </Link>
                        </div>
                        <div class="mr-1 ">
                            <Link
                                class="btn justify-self-end"
                                :href="route('comms.sms-templates')">
                            <span>SMS Templates <span class="bg-info p-1">{{ stats['sms_templates'].created }}</span> <span class=" p-1 bg-success">{{ stats['sms_templates'].active }}</span></span>
                            </Link>
                        </div>
                        <div class="mr-1 mt-1 sm:mt-0">
                            <Link
                                class="btn justify-self-end"
                                :href="route('comms.email-campaigns')">
                            <span>Email Campaigns <span class="bg-info p-1">{{ stats['email_campaigns'].created }}</span> <span class=" p-1 bg-success">{{ stats['email_campaigns'].active }}</span></span>
                            </Link>
                        </div>
                        <div class="mt-1 md:mt-0">
                            <Link
                                class="btn justify-self-end"
                                :href="route('comms.sms-campaigns')">
                            <span>SMS Campaigns <span class="bg-info p-1">{{ stats['sms_campaigns'].created }}</span> <span class=" p-1 bg-success">{{ stats['sms_campaigns'].active }}</span></span>
                            </Link>
                        </div>
                    </div>

                    <div class="flex flex-row justify-center xl:justify-end">
                        <div class="mt-2 mr-1 xl:mt-0">
                            <Link
                                class="btn justify-self-end"
                                href="#" @click="comingSoon()">
                            <span>+ New Email</span>
                            </Link>
                        </div>
                        <div class="mt-2 ml-1 xl:mt-0">
                            <Link
                                class="btn justify-self-end"
                                href="#" @click="comingSoon()">
                            <span>+ New SMS</span>
                            </Link>
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
                        <div class="text-center">
                            There's nothing here! Do something about it.
                        </div>
                    </div>
                </div>
            </div>
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
    props: ['title', 'audiences', 'activeAudience', 'stats'],
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
