<template>
    <LayoutHeader title="Dashboard"> </LayoutHeader>
    <div
        class="d-flex justify-content-center"
        style="width: 50%; height: 100vh; background: white"
    >
        <div class="d-flex justify-content-center">
            <iframe
                :src="PDF"
                style="width: 100%; height: 75vh; border: none"
                class="pb-5 d-flex"
            >
                Oops! an error has occurred.
            </iframe>
            <button
                type="button"
                @click="showSignatureModal"
                class="btn btn-success text-white"
                :disabled="PDFSigned"
            >
                <label class="uppercase">Sign PDF</label>
            </button>
            <daisy-modal
                title="Signature Pad"
                overlayTheme="dark"
                modal-theme="dark"
                ref="signatureModal"
            >
                <SignatureComponent
                    @savePdfSignature="signThePdf"
                    :user-name="userName"
                ></SignatureComponent>
            </daisy-modal>
        </div>
    </div>
    <daisy-modal title="Deployment Announcement" ref="noUrlFound">
        <div class="p-6">URL for the signed PDF was not found!</div>
    </daisy-modal>
</template>

<script setup>
import { ref, onMounted, watch, onUnmounted } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
import DaisyModal from "@/Components/DaisyModal.vue";
import SignatureComponent from "./SignatureCanvas.vue";

const props = defineProps({
    pdfUrl: {
        type: String,
    },
    agreementId: {
        type: String,
    },
    userName: {
        type: String,
        default: "James Doe",
    },
    fileName: {
        type: String,
    },
    isSigned: {
        type: Boolean,
    },
});

const PDF = ref(null);
const noUrlFound = ref(null);
const PDFSigned = ref(false);
const signatureModal = ref(null);
const showSignatureModal = () => {
    signatureModal.value.open();
};

const signThePdf = (data) => {
    axios
        .post(route("agreement.pdf.sign", props.agreementId), {
            pdfUrl: props.pdfUrl,
            signatureFile: data,
            fileName: props.fileName,
        })
        .then((res) => {
            PDF.value = res.data.url;
            if (res.data.url == "") {
                noUrlFound.value.open();
                PDFSigned.value = true;
            }
            signatureModal.value.close();
        });
};

onMounted(() => {
    PDF.value = props.pdfUrl;
    PDFSigned.value = props.isSigned;
});
</script>
