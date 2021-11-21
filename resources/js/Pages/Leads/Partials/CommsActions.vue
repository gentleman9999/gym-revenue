<template>
    <div class="col-span-3">
        <div v-if="activeContactMethod === 'email'">
            <p>Contact Via Email</p>
            <p><i>Enter a subject and a message in the body and click Create Message to send to your customer.</i></p>
            <div class="flex flex-col mb-4">
                <label>Email Subject</label>
                <input class="form-control" v-model="emailSubject" type="text"/>
            </div>
            <div class="flex flex-col mb-4">
                <label>Email Message</label>
                <textarea class="form-control" v-model="emailBody" rows="4" cols="38"></textarea>
            </div>
        </div>
        <div v-if="activeContactMethod === 'phone'">
            <p>Contact Via Phone Call</p>
            <p><i>Use the text box below to jot down notes during the call with your customer. On your phone, or
                voice-enabled browser, click "Call Lead" to contact them instantly!</i></p>
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
            <p><i>This feature is best utilized to remind them of their upcoming appointment or to send them their
                enrollment URL.</i></p>
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
                <div class="mr-4" v-if="activeContactMethod === 'phone'">
                    <jet-button type="button" warning @click="callLead()"><i class="fad fa-phone-volume"></i> Call Lead
                    </jet-button>
                </div>
                <div class="mr-4">
                    <jet-button type="button" success @click="submitLog()"><i class="fad fa-books-medical"></i>
                        {{ submitText }}
                    </jet-button>
                </div>
                <div class="mr-4">
                    <jet-button type="button" error @click="clearForm()"><i class="fad fa-trash"></i> Clear</jet-button>
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
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {faUserCircle} from '@fortawesome/pro-solid-svg-icons';
import {library} from "@fortawesome/fontawesome-svg-core";

library.add(faUserCircle);

export default defineComponent({
    components: {
        JetInput,
        JetButton,
        FormSection,
        FontAwesomeIcon
    },
    props: ['activeContactMethod', 'userId', 'leadId', 'details'],
    watch: {
        smsMsg(msg) {
            this.charsUsed = msg.length;
            this.charsLeft = this.charLimit - msg.length;
        }
    },
    data() {
        return {
            loading: false,
            smsMsg: '',
            charLimit: 130,
            charsUsed: 0,
            charsLeft: 130,
            emailSubject: '',
            emailBody: '',
            dynamicDetails: '',
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

            switch (this.activeContactMethod) {
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
            this.dynamicDetails = this.details;
        },
        callLead() {
            window.open(`tel:${this.phone}`)
        },
        submitLog() {
            let _this = this;
            switch (this.activeContactMethod) {
                case 'email':
                    axios.post(`/data/leads/contact/${this.leadId}`, {
                        //'user_id': this.userId,
                        subject: this.emailSubject,
                        message: this.emailBody,
                        method: this.activeContactMethod
                    },).then(({data}) => {
                        if (data.success) {
                            alert('Sweet!');
                            console.log(_this.details)
                            let newDetails = [];
                            newDetails.push({
                                id: 'temp',
                                'client_id': _this.details[0]['client_id'],
                                field: 'emailed_by_rep',
                                value: _this.userId,
                                misc: {
                                    subject: _this.emailSubject,
                                    message: _this.emailBody,
                                    'user_email': data.email
                                },
                                'created_at': data.time
                            })

                            for (let x in _this.dynamicDetails) {
                                newDetails.push(_this.dynamicDetails[x])
                            }
                            _this.dynamicDetails = newDetails;
                        } else {
                            if ('message' in data) {
                                alert(data.message);
                            } else {
                                alert('Your email was not saved or executed. Please try again');
                            }
                        }

                    })
                        .catch(err => {
                            console.error(err);
                            alert('Ooops an error prevents us from sending your email. Please try again!')
                        })
                    break;

                case 'phone':
                    axios.post(`/data/leads/contact/${this.leadId}`, {
                        //'user_id': this.userId,
                        outcome: this.phoneCallOptions[this.phoneCallOption],
                        notes: this.phoneCallNotes,
                        method: this.activeContactMethod
                    },).then(({data}) => {
                        if (data.success) {
                            alert('Awesome!')
                            let newDetails = [];
                            newDetails.push({
                                id: 'temp',
                                'client_id': _this.details[0]['client_id'],
                                field: 'called_by_rep',
                                value: _this.userId,
                                misc: {
                                    outcome: _this.phoneCallOptions[_this.phoneCallOption],
                                    notes: _this.phoneCallNotes,
                                    'user_email': data.email
                                },
                                'created_at': data.time
                            })

                            for (let x in _this.dynamicDetails) {
                                newDetails.push(_this.dynamicDetails[x])
                            }
                            _this.dynamicDetails = newDetails;
                        } else {
                            if ('message' in data) {
                                alert(data.message);
                            } else {
                                alert('Your phone call was not saved or executed. Please try again');
                            }
                        }
                    })
                        .catch(err => {
                            console.error(err);
                            alert('Ooops an error prevents us from logging your phone call. Please try again!')
                        })
                    break;

                case 'sms':
                    axios.post(`/data/leads/contact/${this.leadId}`, {
                        //'user_id': this.userId,
                        message: this.smsMsg,
                        method: this.activeContactMethod
                    }).then(({data}) => {
                        if (data.success) {
                            alert('Bodacious!')
                            let newDetails = [];
                            newDetails.push({
                                id: 'temp',
                                'client_id': _this.details[0]['client_id'],
                                field: 'sms_by_rep',
                                value: _this.userId,
                                misc: {
                                    message: _this.smsMsg,
                                    'user_email': data.email
                                },
                                'created_at': data.time
                            })

                            for (let x in _this.dynamicDetails) {
                                newDetails.push(_this.dynamicDetails[x])
                            }
                            _this.dynamicDetails = newDetails;
                        } else {
                            if ('message' in data) {
                                alert(data.message);
                            } else {
                                alert('Your text message was not saved or executed. Please try again');
                            }
                        }
                    })
                        .catch(err => {
                            console.error(err);
                            alert('Ooops an error prevents us from sending your text message. Please try again!')
                        })
                    break;
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
        this.fetchLeadInfo()
    }
});
</script>

