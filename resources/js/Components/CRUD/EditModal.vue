<template>
    <daisy-modal id="editModal" ref="editModal" @close="close">
        <ApolloQuery
            :query="(gql) => queries[modelKey].edit"
            :variables="editParam"
            v-if="editParam"
        >
            <template v-slot="{ result: { data, loading, error } }">
                <div v-if="loading">Loading...</div>
                <div v-else-if="error">Error</div>
                <component
                    v-else-if="data"
                    :is="editComponent"
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
import { editParam, clearEditParam } from "@/Components/CRUD/helpers/gqlData";
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
        const editModal = ref(null);

        function open() {
            editModal?.value?.open();
        }

        function close() {
            clearEditParam();
        }

        watchEffect(() => {
            if (editParam.value) {
                open();
            } else {
                close();
            }
        });
        onUnmounted(() => {
            clearEditParam();
        });
        return { close, editModal, editParam, queries };
    },
};
</script>

<style scoped></style>
