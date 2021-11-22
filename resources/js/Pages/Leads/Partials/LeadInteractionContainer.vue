<template>
    <p class="text-center" v-if="loading"> Loading Details about {{ firstName }} {{ lastName }}...</p>
    <div v-else>
        <div class="flex flex-col items-center mb-4">
            <div class="w-full mb-8 mt-4">
                <div class="grid grid-cols-12 w-full gap-4">
                    <div class="col-span-12 lg:col-span-4 flex-shrink-0 bg-base-300 rounded-lg flex flex-col p-4">
                        <font-awesome-icon icon="user-circle" size="6x" class="self-center opacity-10"/>
                        <h1 class="text-center text-2xl">{{ firstName }} {{ lastName }}</h1>

                        <!--                        <ul class="w-full">-->
                        <!--                            <li class="mb-4"><p><b>Email -</b> {{ email }}</p></li>-->
                        <!--                            <li><p><b>Phone -</b> {{ phone }}</p></li>-->
                        <!--                        </ul>-->
                        <div class="flex flex-row mt-8 self-center" v-if="claimedByUser">
                            <div class="mr-4">
                                <jet-button type="button" success @click="activeContactMethod = 'email'">Email
                                </jet-button>
                            </div>
                            <div class="mr-4">
                                <jet-button type="button" error @click="activeContactMethod = 'phone'">Call</jet-button>
                            </div>
                            <div class="mr-4">
                                <jet-button type="button" info @click="activeContactMethod = 'sms'">SMS</jet-button>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-8 rounded-lg bg-base-300 p-4">
                        <CommsHistory :details="details"/>
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
    </div>
</template>

<script>
import {defineComponent} from 'vue'
import JetInput from '@/Jetstream/Input.vue';
import JetButton from '@/Jetstream/Button.vue';
import FormSection from '@/Jetstream/FormSection.vue'
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
        JetInput,
        JetButton,
        FormSection,
        FontAwesomeIcon,
        SweetModal
    },
    props: ['userId', 'leadId', 'firstName', 'lastName', 'email', 'phone', 'details'],
    data() {
        return {
            loading: false,
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
        }
    },
    mounted() {
        this.fetchLeadInfo()
    }
});
</script>

