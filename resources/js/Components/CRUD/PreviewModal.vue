<template>
    <daisy-modal id="previewModal" ref="previewModal" @close="close">
        <ApolloQuery
            :query="(gql) => queries[modelKey].preview"
            :variables="queryParam"
            v-if="queryParam && purpose === 'preview'"
        >
            <template v-slot="{ result: { data, loading, error } }">
                <div v-if="loading">Loading...</div>
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
    queryParam,
    clearQueryParam,
    purpose,
} from "@/Components/CRUD/helpers/gqlData";
import queries from "@/gql/queries";

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
        const previewModal = ref(null);

        function open() {
            previewModal?.value?.open();
        }

        function close() {
            clearQueryParam();
        }

        watchEffect(() => {
            if (queryParam.value && purpose.value === "preview") {
                console.log("preview");
                open();
            }
        });
        onUnmounted(() => {
            clearQueryParam();
        });
        return { close, previewModal, queryParam, queries, purpose };
    },
};
</script>

<style scoped></style>
