<template>
    <app-layout :title="title">
        <jet-bar-container>

            <div class="bg-base-200 w-full rounded-lg p-4">

                <div class="flex flex-row items-center mb-4">
                    <h2 class="font-semibold text-xl  leading-tight">
                        Leads
                    </h2>
                    <div class="flex-grow"/>
                    <search-filter v-model:modelValue="form.search" class="w-full max-w-md mr-4" @reset="reset">
                        <div class="block py-2 text-xs ">Trashed:</div>
                        <select v-model="form.trashed" class="mt-1 w-full form-select">
                            <option :value="null"/>
                            <option value="with">With Trashed</option>
                            <option value="only">Only Trashed</option>
                        </select>
                    </search-filter>
                    <div
                        class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition">

                    </div>
                </div>

                <div class="flex flex-row items-center mb-4">
                    <div class="hidden space-x-8 sm:-my-px sm:flex pt-6">
                        <a class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                           href="#" @click="comingSoon()">Dashboard</a>
                        <a class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                           href="#" @click="comingSoon()">Calendar</a>
                        <a class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                           href="#" @click="comingSoon()">Leads</a>
                        <a class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                           href="#" @click="comingSoon()">Tasks</a>
                        <a class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                           href="#" @click="comingSoon()">Contacts</a>
                        <a class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                           href="#" @click="comingSoon()">Consultants</a>
                    </div>

                    <div class="flex-grow"/>
                    <inertia-link
                        class="btn btn-success justify-self-end"
                        :href="route('data.leads.create')">
                        <span>Add Lead</span>
                    </inertia-link>
                </div>
            </div>
            <gym-revenue-table :headers="tableHeaders" :resource="leads">
                <tr v-if="leads.data.length === 0">
                    <td></td>
                    <td></td>
                    <td>No Data Available</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="hover" v-else v-for="(lead, idx) in leads.data" :key="idx">
                    <td>{{ getDate(lead.created_at) }}</td>
                    <td>{{ lead.first_name }}</td>
                    <td>{{ lead.last_name }}</td>
                    <td>{{ lead.location?.name }}</td>
                    <td>
                        <div class="badge" :class="badgeClasses(lead.lead_type)">{{ lead.lead_type }}</div>
                    </td>
                    <td>
                        <div :style="checkClaimDetail(idx) === 'Available' ? 'cursor:pointer' : ''"
                             @click="assignLeadToUser(idx)">
                            <jet-bar-badge :text="checkClaimDetail(idx)" :type="checkClaimDetailColor(idx)"/>
                        </div>
                    </td>
                    <td class="flex flex-row justify-center space-x-2">
                        <!-- Availability to be claimed by a Rep status -->


                        <inertia-link class=" hover:"
                              :href="route('data.leads.show', lead.id)" v-if="!lead?.deleted_at">
                            <jet-bar-icon type="message" fill/>
                        </inertia-link>
                        <inertia-link class=" hover:"
                              :href="route('data.leads.edit', lead.id)" v-if="!lead?.deleted_at">
                            <jet-bar-icon type="pencil" fill/>
                        </inertia-link>
                    </td>
                </tr>

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
import GymRevenueTable from "@/Components/CRUD/GymRevenueTable";
import JetBarBadge from "@/Components/JetBarBadge";
import JetBarIcon from "@/Components/JetBarIcon";
import SearchFilter from "@/Components/SearchFilter";
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import LeadInteraction from "./Partials/LeadInteractionContainer"

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
        SearchFilter,
        LeadInteraction,
        GymRevenueTable
    },
    props: ['leads', 'title', 'isClientUser', 'filters'],
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get(this.route('data.leads'), pickBy(this.form), {
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
            activeLead: '',
            assigning: '',
        }
    },
    computed: {
        tableHeaders() {
            return ['date', 'first_name', 'last_name', 'location', 'lead_type', 'status', ''];
        }
    },
    methods: {
        badgeClasses(lead_type) {
            return {
                'badge-primary': lead_type === 'facebook',
                'badge-secondary': lead_type === 'snapchat',
                'badge-info': lead_type === 'free_trial',
                'badge-accent': lead_type === 'instagram',
                'badge-success': lead_type === 'grand_opening',
                'badge-outline': lead_type === 'contact_us',
                'badge-ghost': lead_type === 'app_referral',
                'badge-error': lead_type === 'streaming_preview',
                'badge-warning': lead_type === 'personal_training',

            }
        },
        getDate(date) {
            let newdate = new Date(date);

            return newdate.toDateString();
        },
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        checkClaimDetail(pos) {
            let result = 'Available'
            let lead = this.leads.data[pos];

            if (this.assigning === pos) {
                return 'Assigning...';
            }

            for (let d in lead['details_desc']) {
                let detail = lead['details_desc'][d];
                if (detail.field === 'claimed') {
                    if (parseInt(detail.value) === parseInt(this.$page.props.user.id)) {
                        return 'Yours'
                    } else {
                        return 'Claimed'
                    }
                }
            }

            return result;
        },
        checkClaimDetailColor(pos) {
            let result = 'success'
            let lead = this.leads.data[pos];

            console.log('lead ' + pos, lead);

            if (this.assigning === pos) {
                return 'warning';
            }

            for (let d in lead['details_desc']) {
                let detail = lead['details_desc'][d];
                if (detail.field === 'claimed') {
                    if (parseInt(detail.value) === parseInt(this.$page.props.user.id)) {
                        return 'info'
                    } else {
                        return 'danger'
                    }
                }
            }

            return result;
        },
        assignLeadToUser(pos) {
            if (this.checkClaimDetail(pos) === 'Available') {

                this.assigning = pos;
                this.$inertia.visit(route('data.leads.assign'), {
                    method: 'post',
                    data: {
                        'lead_id': this.leads.data[pos].id,
                        'user_id': this.$page.props.user.id,
                        'client_id': this.leads.data[pos].client_id
                    }
                })
            } else {
                console.log('Unable to execute assign on this lead!');
            }
        },
        comingSoon() {
            new Noty({
                type: 'warning',
                theme: 'sunset',
                text: 'Feature Coming Soon!',
                timeout: 7500
            }).show();
        }
    },
    mounted() {

    }
});
</script>

<style scoped>

</style>
