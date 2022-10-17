<template>
    <daisy-modal id="editModal" ref="editModal" @close="close">
        <ApolloQuery
            :query="(gql) => queries[modelKey].edit"
            :variables="queryParam"
            v-if="queryParam"
        >
            <template v-slot="{ result: { data, loading, error } }">
                <div v-if="loading">Loading...</div>
                <div v-else-if="error">Error</div>
                <component
                    v-else-if="data"
                    :is="editComponent"
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
    clearQueryParam,
    purpose,
} from "@/Components/CRUD/helpers/gqlData";
import queries from "@/gql/queries";

export default {
    components: { DaisyModal },
    props: {
        editComponent: {
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
        const editModal = ref();

        function open() {
            editModal?.value?.open();
        }

        function close() {
            clearQueryParam();
        }

        watchEffect(() => {
            if (queryParam.value && purpose.value === "edit") {
                open();
            }
        });
        onUnmounted(() => {
            clearQueryParam();
        });
        return { close, editModal, queryParam, queries, purpose };
    },
};
</script>

<style scoped></style>
