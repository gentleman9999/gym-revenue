<template>
    <LayoutHeader title="Dashboard"> </LayoutHeader>
    <div
        class="d-flex justify-content-center"
        style="width: 50%; height: 100vh; background: white"
    >
        <div class="d-flex justify-content-center">
            <iframe
                :src="pdfUrl"
                style="width: 100%; height: 75vh; border: none"
                class="pb-5 d-flex"
            >
                Oops! an error has occurred.
            </iframe>
            <button
                type="button"
                @click="showSignatureModal"
                class="btn btn-success text-white"
            >
                <label class="uppercase">Sign PDF</label>
            </button>
            <daisy-modal
                title="Signature Pad"
                overlayTheme="dark"
                modal-theme="dark"
                ref="signatureModal"
            >
                <SignaturePad @savePdfSignature="signThePdf"></SignaturePad>
            </daisy-modal>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
import SignaturePad from "./SignaturePad.vue";
import DaisyModal from "@/Components/DaisyModal.vue";

const props = defineProps({
    pdfUrl: {
        type: String,
    },
    agreementId: {
        type: String,
    },
});
const signatureModal = ref(null);
const showSignatureModal = () => {
    signatureModal.value.open();
};

const signThePdf = (data) => {
    console.log(data);
    Inertia.post(route("agreement.pdf.sign"), {
        id: props.agreementId,
        pdfUrl: props.pdfUrl,
        signatureFile: data,
    });
};
</script>
