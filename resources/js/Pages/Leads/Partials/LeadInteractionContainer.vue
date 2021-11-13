<template>
    <p class="text-center" v-if="loading"> Loading Details about {{ firstName }} {{ lastName }}...</p>
    <div v-else>
        <h1 class="text-center text-2xl">{{ firstName }} {{ lastName }}</h1>

        <div class="flex flex-col items-center mb-4">
            <div class="w-full mb-8 mt-4">
                <form-section>

                    <template #title>
                        Contact Info
                    </template>

                    <template #description>
                        Select a contact method to reach out to your lead!
                    </template>

                    <template #form>
                        <div class="col-span-3">
                            <ul class="w-full">
                                <li class="mb-4"><p><b>Email -</b> {{ email }}</p></li>
                                <li><p><b>Phone -</b> {{ phone }}</p></li>
                            </ul>
                            <div class="flex flex-row mt-8">
                                <div class="mr-4"><jet-button type="button" success @click="activeContactMethod = 'email'">Email</jet-button></div>
                                <div class="mr-4"><jet-button type="button" error @click="activeContactMethod = 'phone'">Call</jet-button></div>
                                <div class="mr-4"><jet-button type="button" info @click="activeContactMethod = 'sms'">SMS</jet-button></div>
                            </div>
                        </div>
                        <div class="col-span-3">
                            <div v-if="activeContactMethod === 'email'">
                                <p>Contact Via Email</p>
                                <p><i>Enter a subject and a message in the body and click Create Message to send to your customer.</i></p>
                                <div class="flex flex-col mb-4">
                                    <label>Email Subject</label>
                                    <input class="form-control" v-model="emailSubject" type="text" />
                                </div>
                                <div class="flex flex-col mb-4">
                                    <label>Email Message</label>
                                    <textarea class="form-control" v-model="emailBody" rows="4" cols="38"></textarea>
                                </div>
                            </div>
                            <div v-if="activeContactMethod === 'phone'">
                                <p>Contact Via Phone Call</p>
                                <p><i>Use the text box below to jot down notes during the call with your customer. On your phone, or voice-enabled browser, click "Call Lead" to contact them instantly!</i></p>
                                <div class="flex flex-col mb-4">
                                    <label>Call Outcome</label>
                                    <select class="form-control" v-model="phoneCallOption">
                                        <option v-for="(txt, val) in phoneCallOptions" :value="val">{{ txt }}</option>
                                    </select>
                                </div>
                                <div class="flex flex-col mb-4">
                                    <label>Call Log/Notes</label>
                                    <textarea class="form-control" v-model="phoneCallNotes" rows="4" cols="38"></textarea>
                                </div>
                            </div>
                            <div v-if="activeContactMethod === 'sms'">
                                <p> Contact Via SMS</p>
                                <p><i>This feature is best utilized to remind them of their upcoming appointment or to send them their enrollment URL.</i></p>
                                <div>
                                    <label>Message</label>
                                    <textarea class="form-control" v-model="smsMsg" rows="4" cols="40" :maxlength="charLimit"></textarea>
                                    <div class="col-md-12" style="text-align: right">
                                        <small>Character Count - {{ charsUsed }}/{{ charsLeft }}</small>
                                    </div>
                                </div>
                            </div>
                            <div v-if="activeContactMethod !== ''">
                                <div class="flex flex-row">
                                    <div class="mr-4" v-if="activeContactMethod === 'phone'"><jet-button type="button" warning @click="callLead()"><i class="fad fa-phone-volume"></i> Call Lead</jet-button></div>
                                    <div class="mr-4"><jet-button type="button" success @click="submitLog()"><i class="fad fa-books-medical"></i> {{ submitText }}</jet-button></div>
                                    <div class="mr-4"><jet-button type="button" error @click="clearForm()"><i class="fad fa-trash"></i> Clear </jet-button></div>
                                </div>
                            </div>
                        </div>

                    </template>
                </form-section>
            </div>

            <div class="w-full mb-8 mt-4">
                <form-section>
                    <template #title>
                        Communication Board
                    </template>

                    <template #description>
                        This Prospect's History.
                    </template>

                    <template #form>
                        <div class="col-span-6">
                            <ul class="w-full">
                                <li class="mb-4" v-for="detail in details">
                                    <p v-if="detail.field === 'manual_create'"><b>Lead Was Manually Created inside GymRevenue By {{ detail.value }} On -</b> {{ detail.created_at }}</p>
                                    <p v-if="detail.field === 'claimed'"><b>Lead Claimed By {{ detail.misc['user_id'] }} On -</b> {{ detail.created_at }}</p>
                                    <p v-if="detail.field === 'created'"><b>Lead Created On -</b> {{ detail.value }}</p>
                                    <p v-if="detail.field === 'updated'"><b>Lead Updated On -</b> {{ detail.created_at }} by {{ detail.value }}</p>
                                </li>
                            </ul>
                        </div>
                    </template>
                </form-section>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue'
import JetInput from '@/Jetstream/Input.vue';
import JetButton from '@/Jetstream/Button.vue';
import FormSection from '@/Jetstream/FormSection.vue'

export default defineComponent({
    name: "LeadInteractionContainer",
    components: {
        JetInput,
        JetButton,
        FormSection
    },
    props: ['leadId', 'firstName', 'lastName', 'email', 'phone', 'details'],
    watch: {
        smsMsg(msg) {
            this.charsUsed = msg.length;
            this.charsLeft = this.charLimit - msg.length;
        }
    },
    data() {
        return {
            loading: false,
            activeContactMethod: '',
            smsMsg: '',
            charLimit: 130,
            charsUsed: 0,
            charsLeft: 130,
            emailSubject: '',
            emailBody: '',
            phoneCallOption: '',
            phoneCallNotes: '',
            phoneCallOptions: {
                '': 'Select an Outcome',
                contacted: 'Spoke with Lead.',
                voicemail: 'Left a Voicemail',
                'hung-up': 'Lead Hung Up',
                'wrong-number': 'Wrong Number',
                appointment: 'An Appointment Was Scheduled',
                sale: 'Made the Sale over the Phone!'
            },
        }
    },
    computed: {
        submitText() {
            let text = '';

            switch(this.activeContactMethod)
            {
                case 'email':
                    text = 'Create Message';
                    break;


                case 'phone':
                case 'sms':
                    text = 'Submit';
                    break;
            }

            return text;
        }
    },
    methods: {
        clearForm() {
            this.smsMsg = '';
            this.emailSubject = '';
            this.emailBody = '';
            this.phoneCallOption = '';
            this.phoneCallNotes = '';
        },
        fetchLeadInfo() {
            //this.loading = true;
        },
        callLead() {
            this.comingSoon();
        },
        submitLog() {
            this.comingSoon();
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
        this.fetchLeadInfo()
    }
});
</script>

<style scoped>

</style>
