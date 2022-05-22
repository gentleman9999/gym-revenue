<template>
    <sweet-modal
        :title="`${modelName.toUpperCase()} PREVIEW`"
        width="50%"
        overlayTheme="dark"
        modal-theme="dark"
        enable-mobile-fullscreen
        ref="modal"
        @close="close"
    >
        <component
            v-if="previewData"
            :is="previewComponent"
            v-bind="{ [modelKey]: previewData }"
            :data="previewData"
        />
    </sweet-modal>
</template>

<script>
import SweetModal from "@/Components/SweetModal3/SweetModal";
import { ref, watchEffect, onUnmounted } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import {
    clearPreviewData,
    previewData,
} from "@/Components/CRUD/helpers/previewData";

export default {
    components: { SweetModal },
    props: {
        previewComponent: {
            required: true,
        },
        modelName: {
            type: String,
            required: true,
        },
        modelKey: {
            type: String,
            required: true,
        },
    },
    setup() {
        const page = usePage();
        const modal = ref();

        function open() {
            modal?.value?.open();
        }

        function close() {
            clearPreviewData();
        }

        watchEffect(() => {
            if (previewData.value) {
                open();
            }
            // else{
            //     close();
            // }
        });
        onUnmounted(() => {
            clearPreviewData();
        });
        return { close, modal, previewData };
    },
};
</script>

<style scoped></style>
