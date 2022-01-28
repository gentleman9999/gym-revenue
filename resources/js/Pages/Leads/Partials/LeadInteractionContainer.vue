<template>
    <div class="flex flex-col items-center mb-4">
        <div class="w-full mb-8 mt-4">
            <div class="grid grid-cols-12 w-full gap-4">
                <div class="col-span-12 lg:col-span-4 flex-shrink-0 bg-base-300 rounded-lg flex flex-col p-4">
                    <inertia-link :href="route('data.leads.edit', leadId)" class="flex flex-col items-center justify-center">

                        <font-awesome-icon icon="user-circle" size="6x" class="self-center opacity-10"/>
                        <h1 class="text-center text-2xl">
                            {{ firstName }} {{ lastName }}
                        </h1>
                        <div class="badge badge-info mt-4" v-if="trialDates?.length">Trial Uses: {{trialDates?.length || 0}}</div>
                        <div class="badge badge-error mt-4" v-if="trialMemberships?.length">Trial Expires: {{new Date(trialMemberships[0].expiry_date).toLocaleString()}}</div>
                    </inertia-link>


                    <!--                        <ul class="w-full">-->
                    <!--                            <li class="mb-4"><p><b>Email -</b> {{ email }}</p></li>-->
                    <!--                            <li><p><b>Phone -</b> {{ phone }}</p></li>-->
                    <!--                        </ul>-->
                    <div class="flex flex-row mt-8 self-center" v-if="claimedByUser">
                        <div class="mr-4">
                            <Button type="button" success @click="activeContactMethod = 'email'">Email
                            </Button>
                        </div>
                        <div class="mr-4">
                            <Button type="button" error @click="activeContactMethod = 'phone'">Call</Button>
                        </div>
                        <div class="mr-4">
                            <Button type="button" info @click="activeContactMethod = 'sms'">SMS</Button>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-8 rounded-lg bg-base-300 p-4">
                    <CommsHistory :details="details" ref="commsHistoryRef"/>
                </div>
                <sweet-modal :title="modalTitle" width="85%" ref="showViewModal" overlayTheme="dark"
                             modal-theme="dark"
                             enable-mobile-fullscreen
                             @close="activeContactMethod = ''">
                    <div class="col-span-12 lg:col-span-8 rounded-lg bg-base-300 p-4">
                        <CommsActions :activeContactMethod="activeContactMethod" :phone="phone" :lead-id="leadId"
                                      @done="$refs.showViewModal.close();"/>
                    </div>
                </sweet-modal>
            </div>
        </div>
    </div>
</template>

<script>
import {defineComponent} from 'vue';
import Button from '@/Components/Button';
import FormSection from '@/Jetstream/FormSection'
import CommsHistory from "./CommsHistory";
import CommsActions from "./CommsActions";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {faUserCircle} from '@fortawesome/pro-solid-svg-icons';
import {library} from "@fortawesome/fontawesome-svg-core";
import SweetModal from "@/Components/SweetModal3/SweetModal";

library.add(faUserCircle);

export default defineComponent({
    name: "LeadInteractionContainer",
    components: {
        CommsHistory,
        CommsActions,

        Button,
        FormSection,
        FontAwesomeIcon,
        SweetModal
    },
    props: ['userId', 'leadId', 'firstName', 'lastName', 'email', 'phone', 'details', 'trialDates', 'trialMemberships'],
    data() {
        return {
            activeContactMethod: '',
            dynamicDetails: '',
        }
    },
    computed: {
        claimedByUser() {
            let r = false;

            for (let idx in this.details) {
                let d = this.details[idx];
                if (d.field === 'claimed') {
                    r = (d.value == this.userId);
                }
            }

            return r;
        },
        modalTitle() {
            switch (this.activeContactMethod) {
                case "email":
                    return "Email Lead";
                case "phone":
                    return "Call Lead";
                case "sms":
                    return "Text Lead";
            }
        }
    },
    methods: {
        goToLeadDetailIndex(index) {
            console.log('goToLeadDetailIndex', index);
            this.$refs.commsHistoryRef.goToLeadDetailIndex(index);
        },
        fetchLeadInfo() {
            this.dynamicDetails = this.details;
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
    watch: {
        activeContactMethod(val) {
            val && this.$refs.showViewModal.open()
        },
    },
    mounted() {
        this.fetchLeadInfo()
    }
});
</script>

