<template>
    <div>
        <input type="checkbox" id="my-modal" class="modal-toggle" />
        <div class="modal" :class="showModal ? 'modal-open' : ''">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Test Msg - {{ templateName }}</h3>
                <p class="py-4" v-if="!loading">{{ readyMsg }}</p>
                <p class="py-4" v-else>Sending...</p>
                <div class="modal-action" v-if="!loading && !done">
                    <button
                        @click="handleCloseTextModal"
                        class="btn btn-error hover:text-white"
                    >
                        No
                    </button>
                    <button
                        @click="handleSendingText"
                        class="btn btn-success hover:text-white ml-2"
                    >
                        Send It!
                    </button>
                </div>
                <div class="modal-action" v-if="!loading && done">
                    <button
                        @click="handleCloseTextModal"
                        class="btn btn-success hover:text-white"
                    >
                        Close Me!
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineEmits, onMounted, watch } from "vue";

const props = defineProps({
    templateId: {
        type: String,
    },
    templateName: {
        type: String,
    },
});

const showModal = ref(false);
const loading = ref(false);
const readyMsg = ref("Are you ready to send this message?");
const done = ref(false);
const emit = defineEmits(["close"]);

watch([props.templateId], () => {
    alert("Fuck! " + props.templateId);
});

function handleCloseTextModal() {
    loading.value = false;
    emit("close");
}
function handleSendingText() {
    loading.value = true;

    axios
        .post("/comms/sms-templates/test", {
            templateId: props.templateId,
        })
        .then(({ data }) => {
            loading.value = false;
            readyMsg.value = "All Sent. Check your mobile device";
            done.value = true;
        })
        .catch(({ response }) => {
            console.log("", response);
            let msg = "Could not send. Retry?";
            if ("message" in response["data"]) {
                msg = `(${response.status}) - ${response.data.message} Retry?`;
            }
            loading.value = false;
            readyMsg.value = msg;
        });
}

onMounted(() => {
    showModal.value = true;
});
</script>

<style scoped></style>
