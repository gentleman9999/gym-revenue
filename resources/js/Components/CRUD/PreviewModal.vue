<template>
    <daisy-modal id="previewModal" ref="previewModal" @close="close">
        <ApolloQuery
            :query="(gql) => queries[modelKey]"
            :variables="queryParam"
            v-if="queryParam"
        >
            <template v-slot="{ result: { data, loading, error } }">
                <div v-if="loading">Loading...</div>
                <div v-else-if="error">Error</div>
                <component
                    v-else-if="data"
                    :is="previewComponent"
                    v-bind="{ ...data }"
                    :data="data"
                />
                <div v-else>No result</div>
            </template>
        </ApolloQuery>
    </daisy-modal>
</template>

<script>
import DaisyModal from "@/Components/DaisyModal.vue";
import { ref, watchEffect, onUnmounted } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import {
    queryParam,
    clearPreviewData,
} from "@/Components/CRUD/helpers/previewData";
import queries from "@/gql/queries";

console.log("queryParam", queryParam);
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
            if (queryParam.value) {
                open();
            }
            // else{
            //     close();
            // }
        });
        onUnmounted(() => {
            clearPreviewData();
        });
        return { close, previewModal, queryParam, queries };
    },
};
</script>

<style scoped></style>
