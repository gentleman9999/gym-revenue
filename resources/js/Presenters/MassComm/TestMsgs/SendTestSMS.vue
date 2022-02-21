<template>
    <div>
        <input type="checkbox" id="my-modal" class="modal-toggle">
        <div class="modal" :class="(showModal) ? 'modal-open' : ''">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Test Msg - {{ templateName }}</h3>
                <p class="py-4" v-if="!loading">{{ readyMsg }}</p>
                <p class="py-4" v-else>Sending...</p>
                <div class="modal-action" v-if="(!loading) && (!done)">
                    <button @click="handleCloseTextModal" class="btn btn-error hover:text-white">
                        No
                    </button>
                    <button @click="handleSendingText" class="btn btn-success hover:text-white ml-2">
                        Send It!
                    </button>
                </div>
                <div class="modal-action" v-if="!loading && done">
                    <button @click="handleCloseTextModal" class="btn btn-success hover:text-white">
                        Close Me!
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "SendTestSMS",
    props: ['templateId', 'templateName'],
    watch: {
        templateId(id) {
            alert('Fuck! '+id);
        }
    },
    data() {
        return {
            showModal: false,
            loading: false,
            readyMsg: 'Are you ready to send this message?',
            done: false
        }
    },
    computed: {

    },
    methods: {
        handleCloseTextModal() {
            this.loading = false;
            this.$emit('close')
        },
        handleSendingText() {
            let _this = this;
            this.loading = true;

            axios.post('/comms/sms-templates/test', {templateId: this.templateId})
                .then(({ data }) => {
                    _this.loading = false;
                    _this.readyMsg = 'All Sent. Check your mobile device'
                    _this.done = true;
                })
                .catch(({response}) => {
                    console.log('', response)
                    let msg = 'Could not send. Retry?'
                    if('message' in response['data'])
                    {
                        msg = `(${response.status}) - ${response.data.message} Retry?`;
                    }
                    _this.loading = false;
                    _this.readyMsg = msg
                });
        }
    },
    mounted() {
        console.log('templateId', this.templateId);
        this.showModal = true;
    }
}
</script>

<style scoped>

</style>
