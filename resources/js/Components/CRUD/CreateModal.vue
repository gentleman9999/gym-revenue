<template>
    <daisy-modal id="createModal" ref="createModal" @close="close">
        <ApolloQuery
            :query="(gql) => queries[modelKey].create"
            :variables="createParam"
            v-if="createParam && queries[modelKey].create"
        >
            <template v-slot="{ result: { data, loading, error } }">
                <div v-if="loading">Loading...</div>
                <div v-else-if="error">Error</div>
                <component
                    v-else-if="data"
                    :is="createComponent"
                    v-bind="{ ...data }"
                    @close="close"
                />
                <div v-else>Loading...</div>
            </template>
        </ApolloQuery>
        <component
            v-else-if="!queries[modelKey]?.create"
            :is="createComponent"
        />
    </daisy-modal>
</template>

<script>
import DaisyModal from "@/Components/DaisyModal.vue";
import { ref, watchEffect, onUnmounted } from "vue";
import {
    createParam,
    clearCreateParam,
} from "@/Components/CRUD/helpers/gqlData";
import queries from "@/gql/queries";

export default {
    components: { DaisyModal },
    props: {
        createComponent: {
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
        const createModal = ref(null);

        function open() {
            createModal?.value?.open();
        }

        function close() {
            clearCreateParam();
        }

        watchEffect(() => {
            if (createParam.value) {
                open();
            } else {
                createModal?.value?.close();
            }
        });
        onUnmounted(() => {
            clearCreateParam();
        });
        return { close, createModal, createParam, queries };
    },
};
</script>

<style scoped></style>
