<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Leads
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
                    :href="route('data.leads.create')">
                    <span>Create Lead</span>
                </Link>
            </div>
            <jet-bar-table :headers="tableHeaders">
                <tr class="hover:bg-gray-50" v-if="leads.data.length === 0">
                    <jet-bar-table-data></jet-bar-table-data>
                    <jet-bar-table-data></jet-bar-table-data>
                    <jet-bar-table-data>No Data Available</jet-bar-table-data>
                    <jet-bar-table-data></jet-bar-table-data>
                    <jet-bar-table-data></jet-bar-table-data>
                    <jet-bar-table-data></jet-bar-table-data>
                </tr>
                <tr class="hover:bg-gray-50" v-else v-for="(lead, idx) in leads.data" :key="idx">
                    <jet-bar-table-data>{{ getDate(lead.created_at) }}</jet-bar-table-data>
                    <jet-bar-table-data>{{ lead.first_name }}</jet-bar-table-data>
                    <jet-bar-table-data>{{ lead.last_name }}</jet-bar-table-data>
                    <jet-bar-table-data>{{ lead.location.name }}</jet-bar-table-data>
                    <jet-bar-table-data>{{ lead.lead_type }}</jet-bar-table-data>
                    <jet-bar-table-data class="flex flex-row justify-center space-x-2">
                        <!-- Availability to be claimed by a Rep status -->
                        <div :style="checkClaimDetail(idx) === 'Available' ? 'cursor:pointer' : ''" @click="assignLeadToUser(idx)">
                            <jet-bar-badge :text="checkClaimDetail(idx)" :type="checkClaimDetailColor(idx)"/>
                        </div>

                        <button class="text-gray-400 hover:text-gray-500" v-if="!lead?.deleted_at" @click="launchModal(idx)">
                            <jet-bar-icon type="message" fill/>
                        </button>
                        <Link class="text-gray-400 hover:text-gray-500"
                              :href="route('data.leads.edit', lead.id)" v-if="!lead?.deleted_at">
                            <jet-bar-icon type="pencil" fill/>
                        </Link>
                    </jet-bar-table-data>
                </tr>
            </jet-bar-table>
            <pagination class="mt-6" :links="leads.links"/>

            <sweet-modal title="Lead Interactions" width="85%" ref="showViewModal" modal-theme="light" @close="activeLead = ''">
                <lead-interaction v-if="activeLead !== ''" :lead-id="leads.data[activeLead].id"
                                  :user-id="$page.props.user.id"
                                  :first-name="leads.data[activeLead].first_name"
                                  :last-name="leads.data[activeLead].last_name"
                                  :email="leads.data[activeLead].email"
                                  :phone="leads.data[activeLead].mobile_phone"
                                  :details="leads.data[activeLead]['details_desc']"
                ></lead-interaction>
                <!-- <iframe width="100%" height="415" :src="route('data.leads.show', this.activeLead)" frameborder="0" allowfullscreen></iframe> -->
            </sweet-modal>
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
import SweetModal from "../../Components/SweetModal3/SweetModal";
import LeadInteraction from "./Partials/LeadInteractionContainer"

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
        SearchFilter,
        SweetModal,
        LeadInteraction
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
            /*
            if (this.isClientUser) {
                return ['name', 'city', 'state', 'active', ''];
            }
             */

            return ['date', 'first_name', 'last_name', 'location', 'lead_type', ''];
        }
    },
    methods: {
        getDate(date) {
            let newdate = new Date(date);

            return newdate.toDateString();
        },
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        launchModal(pos) {
            this.activeLead = pos;
            this.$refs.showViewModal.open();
        },
        checkClaimDetail(pos) {
            let result = 'Available'
            let lead = this.leads.data[pos];

            if(this.assigning === pos) {
                return 'Assigning...';
            }

            for(let d in lead['details_desc']) {
                let detail = lead['details_desc'][d];
                if(detail.field === 'claimed') {
                    if(parseInt(detail.value) === parseInt(this.$page.props.user.id)) {
                        return 'Yours'
                    }
                    else {
                        return 'Claimed'
                    }
                }
            }

            return result;
        },
        checkClaimDetailColor(pos) {
            let result = 'success'
            let lead = this.leads.data[pos];

            console.log('lead '+pos, lead);

            if(this.assigning === pos) {
                return 'warning';
            }

            for(let d in lead['details_desc']) {
                let detail = lead['details_desc'][d];
                if(detail.field === 'claimed') {
                    if(parseInt(detail.value) === parseInt(this.$page.props.user.id)) {
                        return 'info'
                    }
                    else {
                        return 'danger'
                    }
                }
            }

            return result;
        },
        assignLeadToUser(pos) {
            if(this.checkClaimDetail(pos) === 'Available') {

                this.assigning = pos;
                this.$inertia.visit(route('data.leads.assign'), {
                    method: 'post',
                    data: {
                        'lead_id': this.leads.data[pos].id,
                        'user_id': this.$page.props.user.id,
                        'client_id': this.leads.data[pos].client_id
                    }
                })
            }
            else {
                console.log('Unable to execute assign on this lead!');
            }
        }
    },
    mounted() {

    }
});
</script>

<style scoped>

</style>
