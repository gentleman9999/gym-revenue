<template>
    <div>
        <div id="signature">
            <VueSignaturePad
                :options="{
                    onBegin: () => {
                        $refs.signaturePad.resizeCanvas();
                    },
                }"
                ref="signaturePad"
                width="800px"
                height="300px"
            />
        </div>
        <div class="action mt-2">
            <button
                type="button"
                class="btn btn-error"
                error
                outline
                @click="undo"
            >
                Undo
            </button>
            <button class="btn btn-secondary float-right" @click="save">
                Save
            </button>
        </div>
    </div>
</template>
<script setup>
import { ref } from "vue";
import { VueSignaturePad } from "vue-signature-pad";
import Button from "@/Components/Button.vue";
const emit = defineEmits(["save-pdf-signature"]);
const signaturePad = ref(null);

const undo = () => {
    console.log(signaturePad, "2121");
    signaturePad.value.clearSignature();
};
const save = () => {
    const { isEmpty, data } = signaturePad.value.saveSignature();
    if (!isEmpty) {
        emit("save-pdf-signature", data);
    }
};
</script>
<style scoped>
#signature {
    /* border: double 3px transparent; */
    border: 4px solid hsla(var(--s) / var(--tw-border-opacity));
    border-radius: 5px;
    background-image: linear-gradient(white, white),
        radial-gradient(circle at top left, #4bc5e8, #9f6274);
    background-origin: border-box;
    background-clip: content-box, border-box;
}
</style>
