<template>
    <div class="grid grid-cols-1 gap-3">
        <div class="border border-secondary rounded relative flex gap-10">
            <div class="m-5">
                <label class="block font-medium text-sm">
                    Enter the Text
                </label>
                <input class="mt-1" type="text" v-model="form.userInput" />
            </div>
            <div class="m-5">
                <label class="block font-medium text-sm"> Font Family </label>
                <select v-model="form.font" class="mt-1 w-44 form-select">
                    <option value="frank">Frank</option>
                    <option value="tiffany">Tiffany</option>
                    <option value="ernie">Ernie</option>
                </select>
            </div>
        </div>
        <div
            id="capture-signature"
            ref="signaturePad"
            class="text-center bg-base-content text-neutral"
            :class="'tk-adobe-handwriting-' + form.font"
        >
            <div class="p-5">
                {{ form.userInput }}
            </div>
        </div>
        <div class="action">
            <button class="btn btn-secondary float-right" @click="save">
                Save
            </button>
        </div>
    </div>
</template>
<style scoped>
@import url("https://use.typekit.net/zof6tbk.css");
#capture-signature {
    line-height: normal;
    font-size: 60px;
}
</style>
<script setup>
import html2canvas from "html2canvas";
import { ref } from "vue";
const props = defineProps({
    userName: {
        type: String,
    },
});
const form = ref({
    userInput: props.userName,
    font: "tiffany",
});
const signaturePad = ref(null);
const emit = defineEmits(["save-pdf-signature"]);
const save = () => {
    if (form.value.userInput) {
        html2canvas(document.getElementById("capture-signature"), {
            scale: 0.7,
            width: signaturePad.value.clientWidth,
            height: signaturePad.value.clientHeight + 50,
        }).then(function (canvas) {
            emit("save-pdf-signature", canvas.toDataURL());
        });
    }
};
</script>
