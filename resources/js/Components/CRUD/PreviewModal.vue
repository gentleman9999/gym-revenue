<template>
    <daisy-modal id="previewModal" ref="previewModal" @close="close">
        <component
            v-if="previewData"
            :is="previewComponent"
            v-bind="{ [modelKey]: previewData }"
            :data="previewData"
        />
    </daisy-modal>
</template>

<script>
import DaisyModal from "@/Components/DaisyModal.vue";
import { ref, watchEffect, onUnmounted } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import {
    clearPreviewData,
    previewData,
} from "@/Components/CRUD/helpers/previewData";

export default {
    components: { DaisyModal },
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
        const previewModal = ref();

        function open() {
            previewModal?.value?.open();
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
        return { close, previewModal, previewData };
    },
};
</script>

<style scoped></style>
