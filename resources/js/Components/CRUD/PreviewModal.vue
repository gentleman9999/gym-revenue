<template>
    <daisy-modal id="previewModal" ref="previewModal" @close="close">
        <ApolloQuery
            :query="(gql) => queries[modelKey].preview"
            :variables="previewParam"
            v-if="previewParam"
        >
            <template v-slot="{ result: { data, loading, error }, isLoading }">
                <div v-if="isLoading">
                    <spinner />
                </div>
                <div v-else-if="error">Error</div>
                <component
                    v-else-if="data"
                    :is="previewComponent"
                    v-bind="{ ...data }"
                />
                <div v-else>No result</div>
            </template>
        </ApolloQuery>
    </daisy-modal>
</template>

<script>
import DaisyModal from "@/Components/DaisyModal.vue";
import { ref, watchEffect, onUnmounted } from "vue";
import {
    previewParam,
    clearPreviewParam,
} from "@/Components/CRUD/helpers/gqlData";
import queries from "@/gql/queries";
import Spinner from "@/Components/Spinner.vue";

export default {
    components: { DaisyModal, Spinner },
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
        const previewModal = ref(null);

        function open() {
            previewModal?.value?.open();
        }

        function close() {
            clearPreviewParam();
        }

        watchEffect(() => {
            if (previewParam.value) {
                console.log("preview");
                open();
            }
        });
        onUnmounted(() => {
            clearPreviewParam();
        });
        return { close, previewModal, previewParam, queries };
    },
};
</script>

<style scoped></style>
